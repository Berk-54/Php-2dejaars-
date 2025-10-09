<?php
declare(strict_types=1);

namespace Webshop;

class ProductList
{
    /** @var Product[] */
    private array $products = [];

    public function addProduct(Product $product): void
    {
        $this->products[] = $product;
    }

    /** @return Product[] */
    public function getProducts(): array
    {
        return $this->products;
    }
}
