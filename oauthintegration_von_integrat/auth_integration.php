<?php

echo 200;

require_once 'oAuthIntegrationClass.php';

$subdomain = 'integrat'; //Поддомен нужного аккаунта
$client_secret = '95cJXtuCnVhUJ49clmltJ9UMsydt3rzuBoAu0UG8aGH3Hip13cjpBV2UZ2fLijvC';
$redirect_uri = 'https://www.hub.integrat.pro/Murad/test/oauthintegration_von_integrat/auth_integration.php';
$amoDaten = json_decode(json_encode($_GET));

file_put_contents('debug.txt', print_r($amoDaten, 1));

$Integration = new oAuthIntegration($subdomain, $client_secret, $redirect_uri);

$Integration->oAuth($amoDaten);