<?php
declare(strict_types=1);

namespace Hospital;

final class Appointment
{
    private Patient $patient;
    private Doctor $doctor;
    /** @var Nurse[] */
    private array $nurses = [];

    private \DateTimeImmutable $beginTime;
    private \DateTimeImmutable $endTime;

    /** @var Appointment[] */
    private static array $appointments = [];
    private static int $count = 0;

    /**
     * @param Nurse[] $nurses
     */
    public function __construct(
        Patient $patient,
        Doctor $doctor,
        \DateTimeImmutable $beginTime,
        \DateTimeImmutable $endTime,
        array $nurses = []
    ) {
        if ($endTime <= $beginTime) {
            throw new \InvalidArgumentException('End time must be after begin time');
        }

        $this->patient   = $patient;
        $this->doctor    = $doctor;
        $this->beginTime = $beginTime;
        $this->endTime   = $endTime;

        foreach ($nurses as $n) {
            $this->addNurse($n);
        }

        self::$appointments[] = $this;
        self::$count++;
    }

    public function getPatient(): Patient { return $this->patient; }
    public function getDoctor(): Doctor   { return $this->doctor; }

    /** @return Nurse[] */
    public function getNurses(): array
    {
        return $this->nurses;
    }

    public function addNurse(Nurse $nurse): void
    {
        foreach ($this->nurses as $n) {
            if ($n === $nurse) return;
        }
        $this->nurses[] = $nurse;
    }

    public function hasNurse(Nurse $nurse): bool
    {
        foreach ($this->nurses as $n) {
            if ($n === $nurse) return true;
        }
        return false;
    }

    public function getBeginTime(): string
    {
        return $this->beginTime->format('Y-m-d H:i');
    }

    public function getEndTime(): string
    {
        return $this->endTime->format('Y-m-d H:i');
    }

    // duur in uren (decimaal)
    public function getTimeDifference(): float
    {
        return ($this->endTime->getTimestamp() - $this->beginTime->getTimestamp()) / 3600;
    }

    // kosten voor patiënt = uurprijs * uren
    public function getCosts(): float
    {
        return $this->patient->getPaymentPerHour() * $this->getTimeDifference();
    }

    public static function getCount(): int
    {
        return self::$count;
    }

    /** @return Appointment[] */
    public static function all(): array
    {
        return self::$appointments;
    }

    /** @return Appointment[] */
    public static function forStaff(Staff $staff): array
    {
        $out = [];
        foreach (self::$appointments as $a) {
            if ($staff instanceof Doctor && $a->doctor === $staff) $out[] = $a;
            if ($staff instanceof Nurse  && $a->hasNurse($staff))  $out[] = $a;
        }
        return $out;
    }

    /** @return Appointment[] */
    public static function forPatient(Patient $patient): array
    {
        $out = [];
        foreach (self::$appointments as $a) {
            if ($a->patient === $patient) $out[] = $a;
        }
        return $out;
    }
}
