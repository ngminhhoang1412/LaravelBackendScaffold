<?php

namespace App\Common;

use App\Models\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;

class Portal
{
    private $endpoint;
    private $token = '';
    private $client;

    function __construct()
    {
        $this->endpoint = config('services.link.endpoint');
        $this->client = new Client();
        $this->login();
    }

    /**
     * @return array
     */
    private function getHeaders(): array
    {
        return [
            'Content-type' => 'application/json',
            'Authorization' => $this->token ? "Bearer {$this->token}" : null
        ];
    }

    /**
     * @return void
     * Login portal and get token
     */
    private function login()
    {
        try {
            $body = [
                'email' => config('services.metaplus_portal.username'),
                'password' => config('services.metaplus_portal.password')
            ];
            $endpoint = $this->endpoint . '/users/login';

            /** @var Response $result */
            $result = $this->client->post($endpoint, [
                'body' => json_encode($body),
                'headers' => $this->getHeaders()
            ]);
            $data = json_decode($result->getBody());
            $this->token = $data->data->access_token;
        } catch (GuzzleException $e) {
            $message = "{$e->getMessage()}\n{$e->getTraceAsString()}";
            $this->logPortalResponse($message, 'Login');
        }
    }

    /**
     * @param $order
     * Redirect order to portal
     */
    public function redirectOrder($order): ?string
    {
        try {
            $body = [
                'order_no' => $order->order_no,
                'quantity' => $order->remaining
            ];
            $endpoint = $this->endpoint . '/orders/reorder-1dg';
            /** @var Response $res */
            $res = $this->client->post($endpoint, [
                'body' => json_encode($body),
                'headers' => $this->getHeaders()
            ]);
            $message = $res->getBody()->getContents();
            $this->logPortalResponse($message, 'Redirect order success', $order->id);
            return $message;
        } catch (GuzzleException $e) {
            // TODO: recheck unauthorized case
            $message = "{$e->getMessage()}\n{$e->getTraceAsString()}";
            $this->logPortalResponse($message, 'Redirect order fail', $order->id);
            return null;
        }
    }

    /**
     * @param $order
     * @return string|null
     * Update order in portal to 'Completed'
     */
    public function updateOrder($order): ?string
    {
        try {
            $body = [
                'order_no' => $order->order_no,
                'run_count' => $order->count
            ];
            if ($order->remaining <= 0) {
                $body['status'] = 'Completed';
            } else {
                $body['status'] = 'Process';
            }
            $endpoint = $this->endpoint . '/orders/update-job-data';
            $res = $this->client->put($endpoint, [
                'body' => json_encode($body),
                'headers' => $this->getHeaders()
            ]);
            $message = $res->getBody()->getContents();
            $this->logPortalResponse($message, 'Update order success', $order->id);
            return $message;
        } catch (GuzzleException $e) {
            // TODO: recheck unauthorized case
            $message = "{$e->getMessage()}\n{$e->getTraceAsString()}";
            $this->logPortalResponse($message, 'Update order fail', $order->id);
            return null;
        }
    }

    /**
     * @param $message
     * @param $title
     * @param $orderId
     * @return void
     */
    private function logPortalResponse($message, $title, $orderId = null)
    {
        Log::query()
            ->insert([
                'title' => $title,
                'message' => json_encode((object) [
                    'message' => $message,
                    'order_id' => $orderId
                ]),
                'category' => Log::LOG_CATEGORY_ENUM[0]
            ]);
    }
}