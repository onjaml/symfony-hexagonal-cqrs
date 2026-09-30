<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\InMemory;

use App\Catalog\Domain\Model\Product;
use App\Catalog\Domain\Repository\ProductRepositoryInterface;
use App\Catalog\Domain\ValueObject\ProductId;
use App\Shared\Domain\Pagination;

final class InMemoryProductRepository implements ProductRepositoryInterface
{
    /** @var array<string, Product> */
    private array $products = [];

    public function save(Product $product): void
    {
        $this->products[$product->id()->value] = $product;
    }

    public function remove(Product $product): void
    {
        unset($this->products[$product->id()->value]);
    }

    public function ofId(ProductId $id): ?Product
    {
        return $this->products[$id->value] ?? null;
    }

    public function paginate(int $page, int $itemsPerPage): Pagination
    {
        $products = array_values($this->products);
        usort($products, static fn (Product $a, Product $b): int => $b->createdAt() <=> $a->createdAt());

        return new Pagination(
            array_slice($products, ($page - 1) * $itemsPerPage, $itemsPerPage),
            $page,
            $itemsPerPage,
            count($products),
        );
    }
}
