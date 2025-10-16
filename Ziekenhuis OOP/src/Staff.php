<?php
declare(strict_types=1);

namespace Hospital;

abstract class Staff extends Person
{
    // bij Doctor = uurloon; bij Nurse = vast weekloon
    protected float $baseAmount = 0.0;

    abstract public function setSalary(float $amount): void;
    abstract public function getSalary(): float;

    public function getAppointments(): array
    {
        return Appointment::forStaff($this);
    }
}
