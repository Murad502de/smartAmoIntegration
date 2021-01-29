<?php

class oAuthIntegration
{
    private $subdomain = '';
    private $link = '';
    private $oAuthDaten = [];
    private $client_secret = '';
    private $client_id = '';
    private $redirect_uri = '';
    private $errors = [
        400 => 'Bad request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        500 => 'Internal server error',
        502 => 'Bad gateway',
        503 => 'Service unavailable',
    ];

    function __construct($subdomain, $client_secret, $redirect_uri)
    {
        //Поддомен нужного аккаунта
        $this->subdomain = $subdomain;

        //Формируем секретный ключ интеграции
        $this->client_secret = $client_secret;

        $this->redirect_uri = $redirect_uri;

        /*echo 'subdomain: '. $this->subdomain . "<br>";
        echo "client_secret: " . $this->client_secret . "<br>";
        echo "redirect_uri: " . $this->redirect_uri . "<br>";
        echo "===============================<br>";*/
    }

    function oAuth($amoDaten)
    {
        //Формируем URL для запроса
        $link = 'https://' . $this->subdomain . '.amocrm.ru/oauth2/access_token';

        // Формируем массив авторизации
        $this->oAuthDaten = [
            'client_id' => $amoDaten->client_id,
            'client_secret' => $this->client_secret,
            'grant_type' => 'authorization_code',
            'code' => $amoDaten->code,
            'redirect_uri' => $this->redirect_uri,
        ];



        /*echo 'link: '. $link . "<br>";
        echo "===============================<br>";

        echo "oAuthDaten:<br>";
        echo "<pre>"; print_r($this->oAuthDaten); echo "</pre><br>";
        echo "===============================<br>";*/



        $response = $this->serverQuery($link, $this->oAuthDaten);

        $response['when_expires'] = time() + $response['expires_in'] - 400;
        $response['client_id'] = $amoDaten->client_id;
        $response['client_secret'] = $this->client_secret;
        $response['redirect_uri'] = $this->redirect_uri;

        file_put_contents('serveranfragedaten.txt', json_encode($response));

        /*echo 'Serveranfragedaten nach dem oAuth:<br>';
        echo "<pre>"; print_r($response); echo "</pre>";
        echo "===============================<br>";*/
    }

    private function accessTokenUpdate($ServerAnfrageDaten)
    {
        /*echo "gelesene Serveranfragedaten:<br>";
        echo "<pre>"; print_r($ServerAnfrageDaten); echo "</pre>";
        echo "===============================<br>";*/



        //Формируем URL для запроса
        $link = 'https://' . $this->subdomain . '.amocrm.ru/oauth2/access_token';

        /** Соберем данные для запроса */
        $data = [
            'client_id' => $ServerAnfrageDaten->client_id,
            'client_secret' => $ServerAnfrageDaten->client_secret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $ServerAnfrageDaten->refresh_token,
            'redirect_uri' => $this->redirect_uri,
        ];



       /* echo 'link fur Accesstoken aktualisieren: '. $link . "<br>";
        echo "===============================<br>";

        echo "Daten fur accessTokenUpdate:<br>";
        echo "<pre>"; print_r($data); echo "</pre><br>";
        echo "===============================<br>";*/


        
        $response = $this->serverQuery($link, $data);

        $response['when_expires'] = time() + $response['expires_in'] - 400;
        $response['client_id'] = $ServerAnfrageDaten->client_id;
        $response['client_secret'] = $this->client_secret;
        $response['redirect_uri'] = $this->redirect_uri;



        file_put_contents('serveranfragedaten.txt', json_encode($response));



        /*echo 'Serveranfragedaten nach der Accesstokenaktualisierung:<br>';
        echo "<pre>"; print_r($response); echo "</pre>";
        echo "===============================<br>";*/

        return $response;
    }

    function serverQuery($link, $amoDaten, $access_token = '', $queryFlag = false)
    {
        /**
         * Нам необходимо инициировать запрос к серверу.
         * Воспользуемся библиотекой cURL (поставляется в составе PHP).
         * Вы также можете использовать и кроссплатформенную программу cURL, если вы не программируете на PHP.
         */
        $curl = curl_init(); //Сохраняем дескриптор сеанса cURL

        /** Устанавливаем необходимые опции для сеанса cURL  */
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
        curl_setopt($curl,CURLOPT_URL, $link);
        curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
        
        if ($queryFlag) curl_setopt($curl,CURLOPT_HTTPHEADER,['Authorization: Bearer ' . $access_token]);

        curl_setopt($curl,CURLOPT_HEADER, false);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($amoDaten));
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);

        //Инициируем запрос к API и сохраняем ответ в переменную
        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);


        /** 
         * Теперь мы можем обработать ответ, полученный от сервера.
         * Это пример. Вы можете обработать данные своим способом.
        */


        $code = (int)$code;

        try
        {
            // Если код ответа не успешный - возвращаем сообщение об ошибке  
            if ($code < 200 || $code > 204) {
                throw new Exception(isset($this->errors[$code]) ? $this->errors[$code] : 'Undefined error', $code);
            }
        }
        catch(\Exception $e)
        {
            die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
        }


        /**
         * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
         * нам придётся перевести ответ в формат, понятный PHP
         */



        $response = json_decode($out, true);

        /*echo 'Serveranwortcode: '. $code;
        echo "<br>===============================<br>";*/

        /*echo 'Serverantwortdaten:<br>';
        echo "<pre>"; print_r($response); echo "</pre>";
        echo "<br>===============================<br>";*/

        return $response;

        /*
        $access_token = $response['access_token']; //Access токен
        $refresh_token = $response['refresh_token']; //Refresh токен
        $token_type = $response['token_type']; //Тип токена
        $expires_in = $response['expires_in']; //Через сколько действие токена истекает

        */
    }

    function leadCreate($ServerAnfrageDaten)
    {
        //Формируем URL для запроса
        $link = 'https://' . $this->subdomain . '.amocrm.ru/api/v4/leads';

        $access_token = $ServerAnfrageDaten->access_token;
            
        $leadCreateData = [
            [
                "name" => $ServerAnfrageDaten->nameLead,
                "created_by" => 0,
                "price" => 20005,
                "created_at" => time(),
                "responsible_user_id" => 2905369,
                "status_id" => 37391986,
                "pipeline_id" => 3905797,
                "visitor_uid" => $ServerAnfrageDaten->amoField
            ]
        ];

        echo 'link: '. $link . "<br>";
        echo "===============================<br>";

        echo "ServerAnfrageDaten, um Lead zu grunden:<br>";
        echo "<pre>"; print_r($leadCreateData); echo "</pre><br>";
        echo "===============================<br>";

        if (!(time() < (int)$ServerAnfrageDaten->when_expires))
        {
            //echo 'Accesstoken ist abgelaufen<br>';

            $ServerAnfrageDaten = $this->accessTokenUpdate($ServerAnfrageDaten);

            $access_token = $ServerAnfrageDaten['access_token'];

            /*echo 'Serveranfragedaten vor der Lead:<br>';
            echo "<pre>"; print_r($ServerAnfrageDaten); echo "</pre>";
            echo "===============================<br>";*/
        }

        $response = $this->serverQuery($link, $leadCreateData, $access_token, true);

        /*echo 'Serveranfragedaten nach der Lead:<br>';
        echo "<pre>"; print_r($response); echo "</pre>";
        echo "===============================<br>";*/
    }
}
