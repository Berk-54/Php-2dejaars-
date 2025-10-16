<?php
declare(strict_types=1);

namespace Hospital;

abstract class Person
{
    private string $name;

    public function __construct(string $name)
    {
        $this->setName($name);
    }

    abstract protected function resolveRole(): string;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = trim($name);
    }

    public function getRole(): string
    {
        return $this->resolveRole();
    }
}
