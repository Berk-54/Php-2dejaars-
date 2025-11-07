<?php
declare(strict_types=1);

namespace App\Models;

class Student extends Person
{
    public const FEE = 25;

    public static int $total = 0;
    public static int $signedTotal = 0;
    public static int $paidTotal = 0;

    private string $className;
    private bool $signedUp = false;
    private bool $paid = false;

    public function __construct(string $name, string $className)
    {
        parent::__construct($name);
        $this->className = $className;
        self::$total++;
    }

    public function role(): string
    {
        return 'Student';
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function setClassName(string $className): void
    {
        $this->className = $className;
    }

    public function getSignedUp(): bool
    {
        return $this->signedUp;
    }

    public function setSignedUp(bool $signedUp): void
    {
        if ($this->signedUp !== $signedUp) {
            if ($signedUp) {
                self::$signedTotal++;
            } else {
                self::$signedTotal--;
            }
        }
        $this->signedUp = $signedUp;
    }

    public function getPaid(): bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): void
    {
        if ($this->paid !== $paid) {
            if ($paid) {
                self::$paidTotal++;
            } else {
                self::$paidTotal--;
            }
        }
        $this->paid = $paid;
    }

    public function getFeePaid(): int
    {
        return $this->paid ? self::FEE : 0;
    }
}
