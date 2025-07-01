<?php
$ch = curl_init('https://api.mercadolibre.com/oauth/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'grant_type' => 'client_credentials'
]));
$response = curl_exec($ch);
if ($response === false) {
    echo 'Erro cURL: ' . curl_error($ch);
} else {
    echo 'Resposta: ' . $response;
}
curl_close($ch);