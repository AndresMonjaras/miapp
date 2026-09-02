<?php
# $url = "https://jsonplaceholder.typicode.com/posts";

$url = "https://jsonplaceholder.typicode.com/posts";

$data = [
    "usuario" => "mojarra",
    "password" => "2pez"
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

$response = curl_exec($ch);
curl_close($ch);

echo $response;

?> 