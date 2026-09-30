<?php

declare(strict_types=1);

namespace App\Catalog\Domain\Repository;

use App\Catalog\Domain\Model\Product;
use App\Catalog\Domain\ValueObject\ProductId;
use App\Shared\Domain\Pagination;

interface ProductRepositoryInterface
{
    public function save(Product $product): void;

    public function remove(Product $product): void;

    public function ofId(ProductId $id): ?Product;

    /**
     * @return Pagination<Product>
     */
    public function paginate(int $page, int $itemsPerPage): Pagination;
}
