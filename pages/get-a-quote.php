<?php
use PHPMailer\PHPMailer\PHPMailer;

$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}
require_once __DIR__ . '/../includes/functions.php';

$activeForm = $_GET['form'] ?? 'quote';
$forms = ['quote' => 'Request a Quote', 'work' => "Let's Collaborate"];
$errors = ['quote' => [], 'work' => []];
$old = ['quote' => [], 'work' => []];
$successMessage = get_flash('quote_success');
$errorMessage = get_flash('quote_error');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formKey = $_POST['form_key'] ?? 'quote';
    $activeForm = $formKey;
    $old[$formKey] = array_map('sanitize', $_POST);
    $requiredFields = ['first_name', 'last_name', 'email', 'phone', 'city', 'project'];

    if (!csrf_validate($_POST['csrf_token'] ?? '')) {
        $errors[$formKey]['csrf'] = 'Security token expired. Please try again.';
    }

    if (!empty($_POST['organization'])) {
        $errors[$formKey]['honeypot'] = 'Invalid submission.';
    }

    if (is_rate_limited()) {
        $errors[$formKey]['rate'] = 'You have reached the submission limit. Please try again later.';
    }

    foreach ($requiredFields as $field) {
        if (empty(trim($_POST[$field] ?? ''))) {
            $errors[$formKey][$field] = 'This field is required.';
        }
    }

    if (!filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
        $errors[$formKey]['email'] = 'Enter a valid email address.';
    }

    if (!$errors[$formKey]) {
        $data = [
            'form_type' => $formKey,
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'project' => trim($_POST['project'] ?? ''),
            'start_timeline' => trim($_POST['start_timeline'] ?? ''),
            'products_note' => trim($_POST['products_note'] ?? ''),
            'message' => trim($_POST['message'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        try {
            $pdo = get_db();
            $stmt = $pdo->prepare('INSERT INTO leads (form_type, first_name, last_name, email, phone, city, project, start_timeline, products_note, message, created_at) VALUES (:form_type, :first_name, :last_name, :email, :phone, :city, :project, :start_timeline, :products_note, :message, :created_at)');
            $stmt->execute($data);
        } catch (Throwable $e) {
            error_log('Lead insert failed: ' . $e->getMessage());
        }

        $subjectName = trim($data['first_name'] . ' ' . $data['last_name']);
        $subject = ($formKey === 'quote' ? 'New Quote Request' : 'New Collaboration Request') . ' from ' . strip_tags($subjectName);
        $htmlBody = '<h1>' . htmlspecialchars($forms[$formKey], ENT_QUOTES) . '</h1>' .
            '<p><strong>Name:</strong> ' . sanitize($data['first_name']) . ' ' . sanitize($data['last_name']) . '</p>' .
            '<p><strong>Email:</strong> ' . sanitize($data['email']) . '</p>' .
            '<p><strong>Phone:</strong> ' . sanitize($data['phone']) . '</p>' .
            '<p><strong>City:</strong> ' . sanitize($data['city']) . '</p>' .
            '<p><strong>Project:</strong> ' . sanitize($data['project']) . '</p>' .
            '<p><strong>Start Timeline:</strong> ' . sanitize($data['start_timeline']) . '</p>' .
            '<p><strong>Products:</strong> ' . sanitize($data['products_note']) . '</p>' .
            '<p><strong>Message:</strong> ' . nl2br(sanitize($data['message'])) . '</p>';
        $textBody = strip_tags($htmlBody);

        increment_rate_limit();

        if (send_mail($subject, $htmlBody, $textBody)) {
            flash('quote_success', 'Thank you! Your submission has been received. Our team will reach out shortly.');
            reset_rate_limit();
            header('Location: /?page=get-a-quote&form=' . urlencode($formKey));
            exit;
        }

        flash('quote_error', 'We could not send your message at this time. Please email us at ' . EMAIL_PRIMARY . '.');
        header('Location: /?page=get-a-quote&form=' . urlencode($formKey));
        exit;
    }
}

function field_value(array $old, string $formKey, string $name): string
{
    return $old[$formKey][$name] ?? '';
}

function field_error(array $errors, string $formKey, string $name): ?string
{
    return $errors[$formKey][$name] ?? null;
}
?>
<section class="section" aria-labelledby="quote-heading" data-animate>
    <div class="section__header">
        <h1 id="quote-heading">Work with <?= BRAND_NAME; ?></h1>
        <p>Share your project details or career interests. Our specialists will respond within one business day.</p>
        <?php if ($successMessage): ?>
            <div class="alert alert--success" role="alert"><?= sanitize($successMessage); ?></div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <div class="alert alert--error" role="alert"><?= sanitize($errorMessage); ?></div>
        <?php endif; ?>
    </div>
    <div class="forms-grid" data-animate>
        <?php foreach ($forms as $key => $title): ?>
            <?php $formErrors = $errors[$key]; ?>
            <form class="quote-form" method="post" novalidate data-form="<?= $key; ?>" data-animate>
                <input type="hidden" name="csrf_token" value="<?= csrf_token(); ?>">
                <input type="hidden" name="form_key" value="<?= $key; ?>">
                <input type="text" name="organization" value="" tabindex="-1" aria-hidden="true" class="honeypot">
                <h2><?= $title; ?></h2>
                <?php if (!empty($formErrors)): ?>
                    <div class="alert alert--error" role="alert">Please correct the highlighted fields and try again.</div>
                    <?php foreach (['csrf', 'honeypot', 'rate'] as $globalError): ?>
                        <?php if (!empty($formErrors[$globalError])): ?>
                            <p class="field-error"><?= sanitize($formErrors[$globalError]); ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="form-row">
                    <div class="form-field">
                        <label for="<?= $key; ?>-first-name">First name</label>
                        <input id="<?= $key; ?>-first-name" name="first_name" type="text" value="<?= field_value($old, $key, 'first_name'); ?>" autocomplete="given-name" aria-invalid="<?= field_error($errors, $key, 'first_name') ? 'true' : 'false'; ?>">
                        <?php if (field_error($errors, $key, 'first_name')): ?><span class="field-error"><?= field_error($errors, $key, 'first_name'); ?></span><?php endif; ?>
                    </div>
                    <div class="form-field">
                        <label for="<?= $key; ?>-last-name">Last name</label>
                        <input id="<?= $key; ?>-last-name" name="last_name" type="text" value="<?= field_value($old, $key, 'last_name'); ?>" autocomplete="family-name" aria-invalid="<?= field_error($errors, $key, 'last_name') ? 'true' : 'false'; ?>">
                        <?php if (field_error($errors, $key, 'last_name')): ?><span class="field-error"><?= field_error($errors, $key, 'last_name'); ?></span><?php endif; ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-field">
                        <label for="<?= $key; ?>-email">Email</label>
                        <input id="<?= $key; ?>-email" name="email" type="email" value="<?= field_value($old, $key, 'email'); ?>" autocomplete="email" aria-invalid="<?= field_error($errors, $key, 'email') ? 'true' : 'false'; ?>">
                        <?php if (field_error($errors, $key, 'email')): ?><span class="field-error"><?= field_error($errors, $key, 'email'); ?></span><?php endif; ?>
                    </div>
                    <div class="form-field">
                        <label for="<?= $key; ?>-phone">Phone</label>
                        <input id="<?= $key; ?>-phone" name="phone" type="tel" value="<?= field_value($old, $key, 'phone'); ?>" autocomplete="tel" aria-invalid="<?= field_error($errors, $key, 'phone') ? 'true' : 'false'; ?>" placeholder="+91 XXXXX XXXXX">
                        <?php if (field_error($errors, $key, 'phone')): ?><span class="field-error"><?= field_error($errors, $key, 'phone'); ?></span><?php endif; ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-field">
                        <label for="<?= $key; ?>-city">City</label>
                        <input id="<?= $key; ?>-city" name="city" type="text" value="<?= field_value($old, $key, 'city'); ?>" autocomplete="address-level2" aria-invalid="<?= field_error($errors, $key, 'city') ? 'true' : 'false'; ?>">
                        <?php if (field_error($errors, $key, 'city')): ?><span class="field-error"><?= field_error($errors, $key, 'city'); ?></span><?php endif; ?>
                    </div>
                    <div class="form-field">
                        <label for="<?= $key; ?>-project">Project Scope</label>
                        <input id="<?= $key; ?>-project" name="project" type="text" value="<?= field_value($old, $key, 'project'); ?>" autocomplete="off" aria-invalid="<?= field_error($errors, $key, 'project') ? 'true' : 'false'; ?>">
                        <?php if (field_error($errors, $key, 'project')): ?><span class="field-error"><?= field_error($errors, $key, 'project'); ?></span><?php endif; ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-field">
                        <label for="<?= $key; ?>-timeline">Expected Start Timeline</label>
                        <select id="<?= $key; ?>-timeline" name="start_timeline">
                            <option value="">Select timeline</option>
                            <?php foreach (['Immediate', '1-3 months', '3-6 months', '6+ months'] as $option): ?>
                                <option value="<?= $option; ?>" <?= field_value($old, $key, 'start_timeline') === $option ? 'selected' : ''; ?>><?= $option; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="<?= $key; ?>-products">Products of Interest</label>
                        <input id="<?= $key; ?>-products" name="products_note" type="text" value="<?= field_value($old, $key, 'products_note'); ?>" autocomplete="off">
                    </div>
                </div>
                <div class="form-field">
                    <label for="<?= $key; ?>-message">Additional Details</label>
                    <textarea id="<?= $key; ?>-message" name="message" rows="4" aria-invalid="<?= field_error($errors, $key, 'message') ? 'true' : 'false'; ?>"><?= field_value($old, $key, 'message'); ?></textarea>
                </div>
                <button class="btn btn-primary" type="submit">Submit</button>
            </form>
        <?php endforeach; ?>
    </div>
</section>
