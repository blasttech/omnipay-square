<?php

namespace Omnipay\Square\Message;

use Omnipay\Common\Message\AbstractRequest;
use SquareConnect;

/**
 * Square Create Credit Card Request
 */
class CreateCardRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://connect.squareup.com';
    protected $testEndpoint = 'https://connect.squareupsandbox.com';

    public function getAccessToken()
    {
        return $this->getParameter('accessToken');
    }

    public function setAccessToken($value)
    {
        return $this->setParameter('accessToken', $value);
    }

    public function setCustomerReference($value)
    {
        return $this->setParameter('customerReference', $value);
    }

    public function getCustomerReference()
    {
        return $this->getParameter('customerReference');
    }

    public function getCard()
    {
        return $this->getParameter('card');
    }

    public function setCard($value)
    {
        return $this->setParameter('card', $value);
    }

    public function getCardholderName()
    {
        return $this->getParameter('cardholderName');
    }

    public function setCardholderName($value)
    {
        return $this->setParameter('cardholderName', $value);
    }

    public function getEndpoint()
    {
        return $this->getTestMode() === true ? $this->testEndpoint : $this->liveEndpoint;
    }

    private function getApiInstance()
    {
        $api_config = new \SquareConnect\Configuration();
        $api_config->setHost($this->getEndpoint());
        $api_config->setAccessToken($this->getAccessToken());
        $api_client = new \SquareConnect\ApiClient($api_config);

        return new \SquareConnect\Api\CustomersApi($api_client);
    }

    public function getData()
    {
        $data = new SquareConnect\Model\CreateCustomerCardRequest();
//        $data->setCustomerId($this->getCustomerReference());
        $data->setCardNonce('cnon:CBASEB2noQwHztYV2s_buuQn9Us');

        dump($this->getCustomerReference());
        dump($this->getCard());

        return $data;
//
//        $data = [];
//
//        $data['customer_id'] = $this->getCustomerReference();
//        $data['card_nonce'] = $this->getCard();
//        $data['cardholder_name'] = $this->getCardholderName();
//
//        return $data;
    }

    public function sendData($data)
    {
        $api_instance = $this->getApiInstance();

        try {
            $result = $api_instance->createCustomerCard('FJDYW3ZVKCXRKAKYRG9R6918KM', $data);

            dd($result);

            if ($error = $result->getErrors()) {
                $response = [
                    'status' => 'error',
                    'code' => $error['code'],
                    'detail' => $error['detail']
                ];
            } else {
                $response = [
                    'status' => 'success',
                    'card' => $result->getCard(),
                    'customerId' => $data['customer_id']
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'detail' => 'Exception when creating card: ' . $e->getMessage()
            ];
        }

        return $this->createResponse($response);
    }

    public function createResponse($response)
    {
        return $this->response = new CardResponse($this, $response);
    }
}
