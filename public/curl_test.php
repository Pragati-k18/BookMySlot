<?php
$ch = curl_init('https://api.openai.com');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    die('Error: ' . curl_error($ch));
} else {
    echo 'Successfully connected to OpenAI';
}
curl_close($ch);