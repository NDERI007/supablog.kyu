<?php
// includes/db.php
require_once 'config.php';

function callSupabase($endpoint, $method = 'GET', $data = null, $token = null, $isStorage = false) {
    $url = SUPABASE_URL . $endpoint;
    
    $headers = [
        'apikey: ' . SUPABASE_KEY,
        'Authorization: Bearer ' . ($token ? $token : SUPABASE_KEY)
    ];

    if (!$isStorage) {
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Prefer: return=representation';
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // SSL Options - Add these lines
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/cacert.pem'); // Path to CA bundle
    
    if ($data) {
        if ($isStorage) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $headers[] = 'Content-Type: image/jpeg';
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    error_log("Supabase Response - Code: $httpCode, Body: $response, cURL Error: $curlError");

    return [
        'code' => $httpCode,
        'data' => json_decode($response, true),
        'raw' => $response,
        'error' => $curlError
    ];
}

// Upload Image Function
function uploadImage($file) {
    $fileName = time() . '_' . basename($file['name']);
    $fileData = file_get_contents($file['tmp_name']);
    
    $endpoint = "/storage/v1/object/blog-images/" . $fileName;
    
    $response = callSupabase($endpoint, 'POST', $fileData, null, true);
    
    if ($response['code'] == 200 || $response['code'] == 201) {
        return SUPABASE_URL . "/storage/v1/object/public/blog-images/" . $fileName;
    }
    
    error_log("Image upload failed: " . print_r($response, true));
    return null;
}
?>