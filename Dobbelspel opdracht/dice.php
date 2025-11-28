<?php
// Dice.php

class Dice
{
    const NUMBER_OF_SIDES = 6;

    private int $faceValue;

    public function __construct()
    {
        // Startwaarde (optioneel)
        $this->throwDice();
    }

    public function throwDice(): void
    {
        $this->faceValue = rand(1, self::NUMBER_OF_SIDES);
    }

    public function getFaceValue(): int
    {
        return $this->faceValue;
    }
}
