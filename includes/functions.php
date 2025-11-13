<?php
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/config.php';

$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function get_db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    return $pdo;
}

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_validate(?string $token): bool
{
    return is_string($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function flash(string $key, $value = null)
{
    if ($value === null) {
        $flash = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $flash;
    }
    $_SESSION['flash'][$key] = $value;
}

function get_flash(string $key, $default = null)
{
    $value = $_SESSION['flash'][$key] ?? $default;
    if (isset($_SESSION['flash'][$key])) {
        unset($_SESSION['flash'][$key]);
    }
    return $value;
}

function is_rate_limited(string $key = SESSION_RATE_LIMIT_KEY): bool
{
    $now = time();
    $attempts = $_SESSION[$key]['attempts'] ?? 0;
    $windowStart = $_SESSION[$key]['window_start'] ?? $now;

    if ($now - $windowStart > RATE_LIMIT_WINDOW) {
        $_SESSION[$key] = ['attempts' => 0, 'window_start' => $now];
        return false;
    }

    if ($attempts >= RATE_LIMIT_MAX) {
        return true;
    }

    $_SESSION[$key]['attempts'] = $attempts;
    $_SESSION[$key]['window_start'] = $windowStart;
    return false;
}

function increment_rate_limit(string $key = SESSION_RATE_LIMIT_KEY): void
{
    $now = time();
    $windowStart = $_SESSION[$key]['window_start'] ?? $now;
    if ($now - $windowStart > RATE_LIMIT_WINDOW) {
        $_SESSION[$key] = ['attempts' => 1, 'window_start' => $now];
        return;
    }

    $_SESSION[$key]['attempts'] = ($_SESSION[$key]['attempts'] ?? 0) + 1;
    $_SESSION[$key]['window_start'] = $windowStart;
}

function reset_rate_limit(string $key = SESSION_RATE_LIMIT_KEY): void
{
    unset($_SESSION[$key]);
}

function send_mail(string $subject, string $htmlBody, string $textBody = ''): bool
{
    if (!class_exists(PHPMailer::class)) {
        error_log('PHPMailer is not available.');
        return false;
    }

    $mailer = new PHPMailer(true);

    try {
        $mailer->isSMTP();
        $mailer->Host = SMTP_HOST;
        $mailer->Port = SMTP_PORT;
        $mailer->SMTPAuth = true;
        $mailer->Username = SMTP_USER;
        $mailer->Password = SMTP_PASS;
        $mailer->SMTPSecure = SMTP_ENCRYPTION;

        $mailer->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mailer->addAddress(EMAIL_PRIMARY, BRAND_NAME);

        $mailer->Subject = $subject;
        $mailer->isHTML(true);
        $mailer->Body = $htmlBody;
        $mailer->AltBody = $textBody ?: strip_tags($htmlBody);

        return $mailer->send();
    } catch (PHPMailerException $e) {
        error_log('Email send failed: ' . $e->getMessage());
        return false;
    }
}

function render_template(string $template, array $data = []): void
{
    extract($data);
    include $template;
}

function paginate(int $page, int $perPage, int $total): array
{
    $totalPages = max(1, (int)ceil($total / $perPage));
    $page = max(1, min($page, $totalPages));
    $offset = ($page - 1) * $perPage;

    return [
        'page' => $page,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'per_page' => $perPage,
    ];
}

function excerpt(string $text, int $length = 160): string
{
    $clean = trim(strip_tags($text));
    if (mb_strlen($clean) <= $length) {
        return $clean;
    }
    return mb_substr($clean, 0, $length - 1) . '…';
}

function current_url_without_param(string $param): string
{
    $query = $_GET;
    unset($query[$param]);
    $base = strtok($_SERVER['REQUEST_URI'], '?');
    $queryString = http_build_query($query);
    return $queryString ? $base . '?' . $queryString : $base;
}
