<?php

declare(strict_types=1);

namespace App\Catalog\Application\Query\FindProducts;

use App\Catalog\Domain\Model\Product;
use App\Catalog\Domain\Repository\ProductRepositoryInterface;
use App\Shared\Application\Query\QueryHandlerInterface;
use App\Shared\Domain\Pagination;

final readonly class FindProductsHandler implements QueryHandlerInterface
{
    public function __construct(private ProductRepositoryInterface $products)
    {
    }

    /**
     * @return Pagination<Product>
     */
    public function __invoke(FindProductsQuery $query): Pagination
    {
        return $this->products->paginate(max(1, $query->page), max(1, $query->itemsPerPage));
    }
}
