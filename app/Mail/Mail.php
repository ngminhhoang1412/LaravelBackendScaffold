<?php

namespace App\Mail;

use App\Common\Constant as Constant;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Mail
{
    /**
     * @throws GuzzleException
     */
    public static function sendMail($toMail, $subject, $htmlContent = "<html><body><h1>Hello, World!</h1></body></html>")
    {
        $url = 'https://rapidprod-sendgrid-v1.p.rapidapi.com/mail/send';
        $sender = env('Mail_USERNAME');
        $data = array(
            "personalizations" => array(
                array(
                    "to" => array(
                        array(
                            "email" => $toMail
                        )
                    )
                )
            ),
            "from" => array(
                "email" => $sender
            ),
            "subject" => $subject,
            "content" => array(
                array(
                    "type" => "text/html",
                    "value" => $htmlContent
                )
            )
        );

        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'X-RapidAPI-Host' => Constant::Mail_X_RapidAPI_Host,
                'X-RapidAPI-Key' =>  env("X_RAPIDAPI_KEY")
            ]
        ]);

        $response = $client->post($url, [
            'json' => $data
        ]);

        $status_code = $response->getStatusCode();
        $response_body = $response->getBody()->getContents();

        echo "Status Code: " . $status_code . "\n";
        echo "Response: " . $response_body . "\n";
    }
}
