<?php
// Game.php
require_once 'Dice.php';

class Game
{
    private array $dice = [];
    private int $throwCount;
    private int $maxThrows;
    private array $history = []; // elke worp: ['values'=>[...], 'sum'=>x, 'score'=>y]
    private ?string $specialMessage = null;

    public function __construct(int $numberOfDice = 5, int $maxThrows = 3)
    {
        $this->maxThrows = $maxThrows;
        $this->throwCount = 0;

        for ($i = 0; $i < $numberOfDice; $i++) {
            $this->dice[] = new Dice();
        }
    }

    public function play(): void
    {
        if ($this->isFinished()) {
            return;
        }

        $this->throwCount++;
        $this->specialMessage = null;

        // Alle dobbelstenen gooien
        foreach ($this->dice as $die) {
            $die->throwDice();
        }

        // Waarden verzamelen
        $values = $this->getDiceValues();
        $sum = array_sum($values);

        // EXTRA FUNCTIONALITEIT:
        // Score = som van de ogen, maar bij 5 gelijke +50 bonus en speciaal bericht.
        $score = $sum;
        if ($this->allDiceEqual()) {
            $score += 50;
            $this->specialMessage =
                "🎉 YAHTZEE! Alle dobbelstenen tonen " . $values[0] . ". Bonus +50 punten!";
        }

        // Scorebord / geschiedenis bijwerken
        $this->history[] = [
            'throw' => $this->throwCount,
            'values' => $values,
            'sum' => $sum,
            'score' => $score,
        ];
    }

    public function getDiceValues(): array
    {
        $values = [];
        foreach ($this->dice as $die) {
            $values[] = $die->getFaceValue();
        }
        return $values;
    }

    public function getThrowCount(): int
    {
        return $this->throwCount;
    }

    public function getMaxThrows(): int
    {
        return $this->maxThrows;
    }

    public function isFinished(): bool
    {
        return $this->throwCount >= $this->maxThrows;
    }

    public function getHistory(): array
    {
        return $this->history;
    }

    public function getSpecialMessage(): ?string
    {
        return $this->specialMessage;
    }

    public function reset(): void
    {
        $this->throwCount = 0;
        $this->history = [];
        $this->specialMessage = null;

        // opnieuw initialiseren (optioneel)
        foreach ($this->dice as $die) {
            $die->throwDice();
        }
    }

    private function allDiceEqual(): bool
    {
        $values = $this->getDiceValues();
        return count(array_unique($values)) === 1;
    }

    // EXTRA FUNCTIONALITEIT: SVG-weergave voor een dobbelwaarde (+ optionele kleur)
    public function generateSvgForValue(int $value, string $color = 'white'): string
    {
        $svg = "<svg width='60' height='60' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg' style='margin:5px; border: 1px solid #000;'>";
        $svg .= "<rect width='100' height='100' style='fill: {$color};'/>";

        $ogenPosities = [
            1 => [[50, 50]],
            2 => [[30, 30], [70, 70]],
            3 => [[30, 30], [50, 50], [70, 70]],
            4 => [[30, 30], [30, 70], [70, 30], [70, 70]],
            5 => [[30, 30], [30, 70], [50, 50], [70, 30], [70, 70]],
            6 => [[30, 30], [30, 50], [30, 70], [70, 30], [70, 50], [70, 70]],
        ];

        foreach ($ogenPosities[$value] as $positie) {
            $svg .= "<circle cx='{$positie[0]}' cy='{$positie[1]}' r='8' fill='black'/>";
        }
        $svg .= "</svg>";

        return $svg;
    }

    /**
     * EXTRA FUNCTIONALITEIT:
     * Bepaal een kleur voor dobbelstenen: als alle waarden gelijk zijn -> lichtgroen,
     * anders wit.
     */
    public function getDiceColor(): string
    {
        if ($this->allDiceEqual()) {
            return '#ccffcc'; // lichtgroen
        }
        return 'white';
    }

    public function getTotalScore(): int
    {
        $total = 0;
        foreach ($this->history as $row) {
            $total += $row['score'];
        }
        return $total;
    }
}
