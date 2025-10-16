<?php
declare(strict_types=1);

namespace Hospital;

final class Nurse extends Staff
{
    private float $bonusHourlyRate = 0.0;

    protected function resolveRole(): string
    {
        return 'Nurse';
    }

    // vast weekloon (40u)
    public function setSalary(float $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Weekly base salary must be >= 0');
        }
        $this->baseAmount = $amount;
    }

    public function getWeeklyBaseSalary(): float
    {
        return $this->baseAmount;
    }

    public function setBonusHourlyRate(float $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Bonus hourly rate must be >= 0');
        }
        $this->bonusHourlyRate = $amount;
    }

    public function getBonusHourlyRate(): float
    {
        return $this->bonusHourlyRate;
    }

    // salaris = vast weekloon + bonus * uren waar nurse aanwezig was
    public function getSalary(): float
    {
        $hours = 0.0;
        foreach ($this->getAppointments() as $a) {
            if ($a->hasNurse($this)) {
                $hours += $a->getTimeDifference();
            }
        }
        return $this->baseAmount + ($hours * $this->bonusHourlyRate);
    }
}
