<?php

declare(strict_types=1);

namespace App\Catalog\Application\Query\FindProduct;

use App\Shared\Application\Query\QueryInterface;

final readonly class FindProductQuery implements QueryInterface
{
    public function __construct(public string $id)
    {
    }
}
