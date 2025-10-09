<?php
declare(strict_types=1);

namespace Webshop;

class Movie extends Product
{
    private string $quality; // bv. DVD / Blueray

    public function __construct(
        string $name,
        float $purchasePrice,
        int $tax,
        float $profit,
        string $description,
        string $quality
    ) {
        parent::__construct($name, $purchasePrice, $tax, $profit, $description);
        $this->quality = $quality;
        $this->setCategory();
    }

    public function setQuality(string $quality): void { $this->quality = $quality; }
    public function getQuality(): string              { return $this->quality; }

    public function setCategory(): void
    {
        $this->setCategoryInternal('Movie');
    }

    public function getInfo(): array
    {
        return [
            'quality' => $this->quality,
        ];
    }
}
