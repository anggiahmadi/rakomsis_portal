<?php

// ============================================================
// Xendit Payment Callback Receiver
// Receives Xendit webhook, then forwards to internal endpoint
// ============================================================

// --- Configuration ---
define('XENDIT_CALLBACK_TOKEN', 'YOUR_XENDIT_CALLBACK_TOKEN'); // Replace with your Xendit callback token
define('FORWARD_ENDPOINT', 'https://portal-dev.rakomsis.com/api/xendit-payment-callback');
define('FORWARD_CALLBACK_TOKEN', 'YOUR_FORWARD_CALLBACK_TOKEN'); // Token for the forwarded request

// --- Set response header ---
header('Content-Type: application/json');

// --- Only allow POST requests ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

// --- Validate Xendit callback token ---
$incomingToken = $_SERVER['HTTP_X_CALLBACK_TOKEN'] ?? '';

if (empty($incomingToken) || $incomingToken !== XENDIT_CALLBACK_TOKEN) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Invalid callback token']);
    exit;
}

// --- Read and validate incoming JSON body ---
$rawBody = file_get_contents('php://input');

if (empty($rawBody)) {
    http_response_code(400);
    echo json_encode(['error' => 'Bad Request: Empty body']);
    exit;
}

$payload = json_decode($rawBody, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Bad Request: Invalid JSON']);
    exit;
}

// --- Forward the payload to the internal endpoint ---
$forwardResult = forwardToEndpoint($rawBody);

if ($forwardResult['success']) {
    http_response_code(200);
    echo json_encode([
        'status'   => 'success',
        'message'  => 'Callback received and forwarded successfully',
        'response' => $forwardResult['body'],
    ]);
} else {
    http_response_code(502);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to forward callback',
        'detail'  => $forwardResult['error'],
    ]);
}

exit;


// ============================================================
// Helper: Forward JSON payload using cURL
// ============================================================
function forwardToEndpoint(string $jsonBody): array
{
    $ch = curl_init(FORWARD_ENDPOINT);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $jsonBody,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'x-callback-token: ' . FORWARD_CALLBACK_TOKEN,
        ],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $responseBody = curl_exec($ch);
    $httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError    = curl_error($ch);
    curl_close($ch);

    if ($responseBody === false || !empty($curlError)) {
        return [
            'success' => false,
            'error'   => 'cURL error: ' . $curlError,
        ];
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        return [
            'success' => false,
            'error'   => "Endpoint returned HTTP {$httpCode}: {$responseBody}",
        ];
    }

    return [
        'success' => true,
        'body'    => $responseBody,
    ];
}
