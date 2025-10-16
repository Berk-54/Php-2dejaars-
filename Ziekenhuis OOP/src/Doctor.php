<?php
declare(strict_types=1);

namespace Hospital;

final class Doctor extends Staff
{
    protected function resolveRole(): string
    {
        return 'Doctor';
    }

    public function setSalary(float $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Hourly rate must be >= 0');
        }
        $this->baseAmount = $amount;
    }

    public function getHourlyRate(): float
    {
        return $this->baseAmount;
    }

    // salaris = som(duur afspraken in uren) * uurloon
    public function getSalary(): float
    {
        $hours = 0.0;
        foreach ($this->getAppointments() as $a) {
            $hours += $a->getTimeDifference();
        }
        return $hours * $this->baseAmount;
    }
}
