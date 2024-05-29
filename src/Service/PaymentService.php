<?php

namespace App\Service;

use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

class PaymentService
{
    /**
     * I'd implement it with custom yaml config if
     * I had enough time
     */
    public const PAYMENT_PROCESSORS = array(
        'stripe' => array(
            'class' => StripePaymentProcessor::class,
            'method' => 'processPayment'
        ),
        'paypal' => array(
            'class' => PaypalPaymentProcessor::class,
            'method' => 'pay'
        )
    );

    public function processPayment(string $method, int|float $price)
    {
        if (!isset(self::PAYMENT_PROCESSORS[$method])) {
            throw new \Exception(
                'Payment method ' . $method . ' does not exist.'
            );
        }

        $class = self::PAYMENT_PROCESSORS[$method]['class'];
        $method = self::PAYMENT_PROCESSORS[$method]['method'];
        $paymentProcessor = new $class;
        $paymentStatus = $paymentProcessor->$method($price);

        if ($paymentStatus === false) {
            throw new \Exception(
                'Your payment has been declined.'
            );
        }
        
        return $paymentProcessor->$method($price);
    }
}
