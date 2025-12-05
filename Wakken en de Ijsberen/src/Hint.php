<?php

class Hint
{
    private string $hintString;

    public function __construct(string $hintString)
    {
        $this->hintString = $hintString;
    }

    public function getHintString(): string
    {
        return $this->hintString;
    }
}