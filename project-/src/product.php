<?php
declare(strict_types=1);

namespace Webshop;

abstract class Product
{
    // Private parent-properties (worden gevuld via constructor/setters in child)
    private string $name;
    private float  $purchasePrice;
    private int    $tax;           // btw in %
    private string $description;
    private float  $profit;        // absolute marge (geen %)
    private string $category = '';

    public function __construct(
        string $name,
        float $purchasePrice,
        int $tax,
        float $profit,
        string $description
    ) {
        $this->name          = $name;
        $this->purchasePrice = $purchasePrice;
        $this->tax           = $tax;
        $this->profit        = $profit;
        $this->description   = $description;
    }

    // ---- Getters & setters (encapsulation) ----
    public function getName(): string            { return $this->name; }
    public function getPurchasePrice(): float    { return $this->purchasePrice; }
    public function getTax(): int               { return $this->tax; }
    public function getProfit(): float          { return $this->profit; }
    public function getDescription(): string     { return $this->description; }
    public function getCategory(): string        { return $this->category; }

    protected function setCategoryInternal(string $category): void
    {
        // alleen door children te gebruiken (vult private parent-property)
        $this->category = $category;
    }

    // Verkoopprijs = (inkoop + winst) * (1 + btw%)
    public function getSalesPrice(): float
    {
        $base = $this->purchasePrice + $this->profit;
        return round($base * (1 + ($this->tax / 100)), 2);
    }

    // ---- Abstracte eisen uit de opdracht ----
    abstract public function getInfo(): array;      // alle productinfo
    abstract public function setCategory(): void;   // stelt de categorie van dit type in
}
