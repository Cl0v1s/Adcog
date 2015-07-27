<?php

namespace Adcog\DefaultBundle\Payment;

use Adcog\DefaultBundle\Entity\PaymentPaypal;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class Paypal
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 *         adcog-buyer@gmail.com
 *         adcogbuyer
 */
class Paypal
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $account;

    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var Client
     */
    private $client;

    /**
     * @param RouterInterface $router   Router
     * @param EntityManager   $em       Manager
     * @param string          $account  Account
     * @param string          $endpoint Endpoint
     * @param string          $clientId Client ID
     * @param string          $secret   Secret
     */
    public function __construct(RouterInterface $router, EntityManager $em, $account, $endpoint, $clientId, $secret)
    {
        $this->router = $router;
        $this->em = $em;

        // Paypal infos
        $this->account = $account;
        $this->endpoint = $endpoint;
        $this->clientId = $clientId;
        $this->secret = $secret;

        // Create shared client
        $this->client = new Client();
    }

    /**
     * Cancel
     *
     * @param PaymentPaypal $payment
     */
    public function cancel(PaymentPaypal $payment)
    {
        $payment->invalidate();
        $this->em->flush();
    }

    /**
     * Execute payment
     *
     * @param PaymentPaypal $payment Payment
     * @param string        $payerId Payer ID
     *
     * @return null|PaymentPaypal
     */
    public function complete(PaymentPaypal $payment, $payerId)
    {
        try {
            $payment->setPayerId($payerId);
            $this->em->flush();
            $payment->setPayment($paymentData = $this->completePayment($payment));
            $this->em->flush();
            if (false === array_key_exists('state', $paymentData) || 'approved' !== $paymentData['state']) {
                throw new \Exception();
            }
            $payment->validate();
            $this->em->flush();

            return $payment;
        } catch (\Exception $e) {
            $payment->invalidate();
            $this->em->flush();
        }

        return null;
    }

    /**
     * Execute payment
     *
     * @param PaymentPaypal $payment
     *
     * @return null|string
     */
    public function execute(PaymentPaypal $payment)
    {
        try {
            $this->em->flush();
            $payment->setToken($this->getAccessToken());
            $this->em->flush();
            $payment->setPayment($paymentData = $this->createPayment($payment));
            $this->em->flush();

            // Find redirect link
            if (array_key_exists('links', $paymentData)) {
                foreach ($paymentData['links'] as $link) {
                    if (array_key_exists('rel', $link) && 'approval_url' === $link['rel'] && array_key_exists('href', $link)) {
                        return $link['href'];
                    }
                }
            }
        } catch (\Exception $e) {
            $payment->invalidate();
            $this->em->flush();
        }

        return null;
    }

    /**
     * Complete payment
     *
     * @param PaymentPaypal $payment
     *
     * @return mixed
     */
    private function completePayment(PaymentPaypal $payment)
    {
        return $this->client->post('https://' . $this->endpoint . sprintf('/v1/payments/payment/%s/execute/', $payment->getPaymentId()), [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => sprintf('Bearer %s', $payment->getTokenId()),
            ],
            'json' => [
                'payer_id' => $payment->getPayerId(),
            ],
        ])->json();
    }

    /**
     * Create payment
     *
     * @param PaymentPaypal $payment
     *
     * @return array
     */
    private function createPayment(PaymentPaypal $payment)
    {
        return $this->client->post('https://' . $this->endpoint . '/v1/payments/payment', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => sprintf('Bearer %s', $payment->getTokenId()),
            ],
            'json' => [
                'intent' => 'sale',
                'redirect_urls' => [
                    'return_url' => $this->router->generate('user_payment_complete', ['payment' => $payment->getId()], RouterInterface::ABSOLUTE_URL),
                    'cancel_url' => $this->router->generate('user_payment_cancel', ['payment' => $payment->getId()], RouterInterface::ABSOLUTE_URL),
                ],
                'payer' => [
                    'payment_method' => 'paypal',
                ],
                'transactions' => [
                    [
                        'amount' => [
                            'total' => $payment->getAmount(),
                            'currency' => 'EUR',
                        ],
//                        'description' => $payment->getDescription(),
                    ],
                ],
            ],
        ])->json();
    }

    /**
     * GetAccessToken
     *
     * @return array
     */
    private function getAccessToken()
    {
        return $this->client->post('https://' . $this->endpoint . '/v1/oauth2/token', [
            'auth' => [
                $this->clientId,
                $this->secret
            ],
            'headers' => [
                'Accept' => 'application/json',
            ],
            'body' => [
                'grant_type' => 'client_credentials',
            ],
        ])->json();
    }
}
