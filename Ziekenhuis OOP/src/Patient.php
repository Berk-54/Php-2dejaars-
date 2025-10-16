<?php
declare(strict_types=1);

namespace Hospital;

final class Patient extends Person
{
    private float $paymentPerHour = 0.0;

    protected function resolveRole(): string
    {
        return 'Patient';
    }

    public function setPaymentPerHour(float $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Payment per hour must be >= 0');
        }
        $this->paymentPerHour = $amount;
    }

    public function getPaymentPerHour(): float
    {
        return $this->paymentPerHour;
    }
}
