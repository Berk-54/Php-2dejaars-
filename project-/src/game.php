<?php
declare(strict_types=1);

namespace Webshop;

class Game extends Product
{
    private string $genre;
    /** @var string[] */
    private array $requirements = [];

    public function __construct(
        string $name,
        float $purchasePrice,
        int $tax,
        float $profit,
        string $description,
        string $genre
    ) {
        parent::__construct($name, $purchasePrice, $tax, $profit, $description);
        $this->genre = $genre;
        $this->setCategory();
    }

    public function setGenre(string $genre): void { $this->genre = $genre; }
    public function getGenre(): string            { return $this->genre; }

    public function addRequirement(string $requirement): void
    {
        $this->requirements[] = $requirement;
    }

    /** @return string[] */
    public function getRequirements(): array
    {
        return $this->requirements;
    }

    public function setCategory(): void
    {
        $this->setCategoryInternal('Game');
    }

    public function getInfo(): array
    {
        return [
            'genre'        => $this->genre,
            'requirements' => $this->requirements,
        ];
    }
}
