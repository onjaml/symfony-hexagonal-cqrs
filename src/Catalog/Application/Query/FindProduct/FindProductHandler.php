<?php

declare(strict_types=1);

namespace App\Catalog\Application\Query\FindProduct;

use App\Catalog\Domain\Exception\ProductNotFound;
use App\Catalog\Domain\Model\Product;
use App\Catalog\Domain\Repository\ProductRepositoryInterface;
use App\Catalog\Domain\ValueObject\ProductId;
use App\Shared\Application\Query\QueryHandlerInterface;

final readonly class FindProductHandler implements QueryHandlerInterface
{
    public function __construct(private ProductRepositoryInterface $products)
    {
    }

    public function __invoke(FindProductQuery $query): Product
    {
        $id = ProductId::fromString($query->id);

        return $this->products->ofId($id) ?? throw ProductNotFound::withId($id);
    }
}
