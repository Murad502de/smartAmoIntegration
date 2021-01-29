<?php

require_once 'oAuthIntegrationClass.php';

$subdomain = 'integrat'; //Поддомен нужного аккаунта
$client_secret = '95cJXtuCnVhUJ49clmltJ9UMsydt3rzuBoAu0UG8aGH3Hip13cjpBV2UZ2fLijvC';
$redirect_uri = 'https://www.hub.integrat.pro/Murad/test/oauthintegration_von_integrat/auth_integration.php';
$amoDaten = json_decode(file_get_contents('serveranfragedaten.txt'));
$amoDaten->nameLead = $_POST['nameLead'];
$amoDaten->amoField = $_POST['amoField'];


echo "<pre>"; print_r($amoDaten); echo "</pre><br>";

$Integration = new oAuthIntegration($subdomain, $client_secret, $redirect_uri);

$Integration->leadCreate($amoDaten);

