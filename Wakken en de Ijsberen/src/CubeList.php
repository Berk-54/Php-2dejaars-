<?php

class CubeList
{
    private array $cubes = [];

    public function addCube(Cube $cube): void
    {
        $this->cubes[] = $cube;
    }

    public function getCubes(): array
    {
        return $this->cubes;
    }

    public function getAmountCubes(): int
    {
        return count($this->cubes);
    }
}