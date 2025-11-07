<?php
declare(strict_types=1);

namespace App\Models;

class Teacher extends Person
{
    public static int $total = 0;

    public function __construct(string $name)
    {
        parent::__construct($name);
        self::$total++;
    }

    public function role(): string
    {
        return 'Teacher';
    }
}
