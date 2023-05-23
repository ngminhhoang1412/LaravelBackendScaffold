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
        $url = 'https://'.Constant::MAIL_X_RAPIDAPI_HOST.'/mail/send';
        $sender = env('MAIL_USERNAME');
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
                'X-RapidAPI-Host' => Constant::MAIL_X_RAPIDAPI_HOST,
                'X-RapidAPI-Key' =>  env("X_RAPIDAPI_KEY")
            ]
        ]);

       $client->post($url, [
            'json' => $data
        ]);

    }
}
