<?php
declare(strict_types=1);

namespace Webshop;

class Music extends Product
{
    private string $artist;
    /** @var string[] */
    private array $numbers = [];

    public function __construct(
        string $name,
        float $purchasePrice,
        int $tax,
        float $profit,
        string $description,
        string $artist
    ) {
        parent::__construct($name, $purchasePrice, $tax, $profit, $description);
        $this->artist = $artist;
        $this->setCategory(); // vult private parent category
    }

    public function setArtist(string $artist): void { $this->artist = $artist; }
    public function getArtist(): string            { return $this->artist; }

    public function addNumber(string $number): void
    {
        $this->numbers[] = $number;
    }

    /** @return string[] */
    public function getNumbers(): array
    {
        return $this->numbers;
    }

    public function setCategory(): void
    {
        $this->setCategoryInternal('Music');
    }

    public function getInfo(): array
    {
        return [
            'artist'  => $this->artist,
            'numbers' => $this->numbers,
        ];
    }
}
