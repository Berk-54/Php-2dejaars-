<?php

class GameList
{
    private array $games = [];

    public function addGame($game): void
    {
        $this->games[] = $game;
    }

    public function getGames(): array
    {
        return $this->games;
    }

    public function getCurrentGame(): ?Game
    {
        if (empty($this->games)) {
            return null;
        }
        return end($this->games);
    }

    public function getAmountGames(): int
    {
        return count($this->games);
    }
}