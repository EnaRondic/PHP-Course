<?php

interface PaymentProcessor
{
    public function processPayment(float | int $amount): bool;
    public function refundPayment(float | int $amount): bool;
}

abstract class OnlinePaymentProcessor implements PaymentProcessor
{
    protected string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    abstract protected function validateApiKey(): bool;
    abstract protected function executePayment(float | int $amount): bool;
    abstract protected function executeRefund(float | int $amount): bool;

    public function processPayment(float | int $amount): bool
    {
        if (!$this->validateApiKey()) {
            throw new Exception("Invalid API key");
        }
        return $this->executePayment($amount);
    }

    public function refundPayment(float | int $amount): bool
    {
        if (!$this->validateApiKey()) {
            throw new Exception("Invalid API key");
        }
        return $this->executeRefund($amount);
    }
}

final class StripeProcessor extends OnlinePaymentProcessor
{
    protected function validateApiKey(): bool
    {
        return strpos($this->apiKey, 'sk_') === 0;
    }

    protected function executePayment(float | int $amount): bool
    {
        echo "Processing Stripe payment of $amount\n";
        return true;
    }

    protected function executeRefund(float | int $amount): bool
    {
        echo "Processing Stripe refund of $amount\n";
        return true;
    }
}

final class PayPalProcessor extends OnlinePaymentProcessor
{
    protected function validateApiKey(): bool
    {
        return strlen($this->apiKey) === 32;
    }

    protected function executePayment(float | int $amount): bool
    {
        echo "Processing PayPal payment of $amount\n";
        return true;
    }

    protected function executeRefund(float | int $amount): bool
    {
        echo "Processing PayPal refund of $amount\n";
        return true;
    }
}

class CashPaymentProcessor implements PaymentProcessor
{
    public function processPayment(float | int $amount): bool
    {
        echo "Processing cash payment of $amount\n";
        return true;
    }

    public function refundPayment(float | int $amount): bool
    {
        echo "Processing cash refund of $amount\n";
        return true;
    }
}

class OrderProcessor
{
    public function __construct(private PaymentProcessor $paymentProcessor)
    {}

    public function processOrder(float | int $amount, string | array $items): void
    {
        $itemsList = is_array($items) ? implode(', ', $items) : $items;
        echo "Processing order for items: $itemsList\n";

        if ($this->paymentProcessor->processPayment($amount)) {
            echo "Order processed successfully\n";
        } else {
            echo "Order processing failed\n";
        }
    }

    public function refundOrder(float | int $amount): void
    {
        if ($this->paymentProcessor->refundPayment($amount)) {
            echo "Refund processed successfully\n";
        } else {
            echo "Refund failed\n";
        }
    }
}

$stripeProcessor = new StripeProcessor("sk_test_123456");
$paypalProcessor = new PayPalProcessor("12345678901234567890123456789012");
$cashProcessor = new CashPaymentProcessor();

$stripeOrder = new OrderProcessor($stripeProcessor);
$paypalOrder = new OrderProcessor($paypalProcessor);
$cashOrder = new OrderProcessor($cashProcessor);

$stripeOrder->processOrder(100.00, "Book");
$paypalOrder->processOrder(150.00, ["Book", "Movie"]);
$cashOrder->processOrder(50.00, ["Apple", "Orange"]);

$stripeOrder->refundOrder(25.00);
$paypalOrder->refundOrder(50.00);
