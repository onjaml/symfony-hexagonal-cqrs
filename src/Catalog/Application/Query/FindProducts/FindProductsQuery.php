<?php

declare(strict_types=1);

namespace App\Catalog\Application\Query\FindProducts;

use App\Shared\Application\Query\QueryInterface;

final readonly class FindProductsQuery implements QueryInterface
{
    public function __construct(
        public int $page = 1,
        public int $itemsPerPage = 30,
    ) {
    }
}
