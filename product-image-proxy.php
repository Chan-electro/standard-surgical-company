<?php
header('Content-Type: application/json; charset=utf-8');

$url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);

if (!$url || !filter_var($url, FILTER_VALIDATE_URL) || !preg_match('/^https?:\/\//i', $url)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing URL parameter.']);
    exit;
}

$contextHost = parse_url($url, PHP_URL_HOST);
if (!$contextHost) {
    http_response_code(400);
    echo json_encode(['error' => 'Unable to determine host from URL.']);
    exit;
}

$curl = curl_init($url);

curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS => 5,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_USERAGENT => 'StandardSurgicalCompanyBot/1.0',
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
]);

$html = curl_exec($curl);

if ($html === false) {
    $error = curl_error($curl);
    curl_close($curl);
    http_response_code(502);
    echo json_encode(['error' => 'Failed to fetch remote content.', 'details' => $error]);
    exit;
}

$contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE) ?: '';
$httpStatus = curl_getinfo($curl, CURLINFO_RESPONSE_CODE) ?: 0;
curl_close($curl);

if ($httpStatus >= 400) {
    http_response_code($httpStatus);
    echo json_encode(['error' => 'Remote server returned an error status.']);
    exit;
}

if (stripos($contentType, 'text/html') === false && stripos($contentType, 'application/xhtml+xml') === false) {
    http_response_code(415);
    echo json_encode(['error' => 'Unsupported content type from remote resource.']);
    exit;
}

libxml_use_internal_errors(true);
$dom = new DOMDocument();
if (!$dom->loadHTML($html)) {
    libxml_clear_errors();
    http_response_code(500);
    echo json_encode(['error' => 'Unable to parse remote HTML document.']);
    exit;
}
libxml_clear_errors();

$xpath = new DOMXPath($dom);

$imageUrl = extractMetaContent($xpath, [
    "//meta[@property='og:image']/@content",
    "//meta[@property='og:image:url']/@content",
    "//meta[@name='twitter:image']/@content",
    "//meta[@name='twitter:image:src']/@content",
]);

if (!$imageUrl) {
    $imageUrl = extractFirstImage($xpath);
}

if (!$imageUrl) {
    http_response_code(404);
    echo json_encode(['error' => 'No image could be located for the requested URL.']);
    exit;
}

$imageUrl = resolveUrl($url, $imageUrl);

if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
    http_response_code(500);
    echo json_encode(['error' => 'Resolved image URL is invalid.']);
    exit;
}

echo json_encode(['image' => $imageUrl]);

function extractMetaContent(DOMXPath $xpath, array $queries)
{
    foreach ($queries as $query) {
        $nodes = $xpath->query($query);
        if ($nodes && $nodes->length > 0) {
            $content = trim($nodes->item(0)->nodeValue);
            if ($content !== '') {
                return $content;
            }
        }
    }
    return null;
}

function extractFirstImage(DOMXPath $xpath)
{
    $nodes = $xpath->query('//img[@src]/@src');
    if ($nodes && $nodes->length > 0) {
        $src = trim($nodes->item(0)->nodeValue);
        if ($src !== '') {
            return $src;
        }
    }
    return null;
}

function resolveUrl(string $baseUrl, string $relative): string
{
    if ($relative === '') {
        return $baseUrl;
    }

    if (parse_url($relative, PHP_URL_SCHEME) !== null) {
        return $relative;
    }

    if ($relative[0] === '#') {
        return $baseUrl;
    }

    $baseParts = parse_url($baseUrl);
    if (!$baseParts) {
        return $relative;
    }

    $scheme = $baseParts['scheme'] ?? 'https';
    $host = $baseParts['host'] ?? '';
    $port = isset($baseParts['port']) ? ':' . $baseParts['port'] : '';
    $path = $baseParts['path'] ?? '/';

    $path = preg_replace('#/[^/]*$#', '/', $path);

    if (strpos($relative, '//') === 0) {
        return $scheme . ':' . $relative;
    }

    $combined = $path . $relative;

    $segments = [];
    foreach (explode('/', $combined) as $segment) {
        if ($segment === '' || $segment === '.') {
            continue;
        }
        if ($segment === '..') {
            array_pop($segments);
            continue;
        }
        $segments[] = $segment;
    }

    $resolvedPath = '/' . implode('/', $segments);

    return $scheme . '://' . $host . $port . $resolvedPath;
}
