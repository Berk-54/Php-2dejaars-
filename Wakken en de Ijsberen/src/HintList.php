<?php

class HintList
{
    private array $hints = [];

    public function addHint(Hint $hint): void
    {
        $this->hints[] = $hint;
    }

    public function getHints(): array
    {
        return $this->hints;
    }

    public function getRandomHint(): ?Hint
    {
        if (empty($this->hints)) {
            return null;
        }
        $randomIndex = array_rand($this->hints);
        return $this->hints[$randomIndex];
    }
}