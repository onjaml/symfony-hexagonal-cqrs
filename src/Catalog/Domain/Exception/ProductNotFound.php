<?php

declare(strict_types=1);

namespace App\Catalog\Domain\Exception;

use App\Catalog\Domain\ValueObject\ProductId;
use App\Shared\Domain\Exception\NotFoundException;

final class ProductNotFound extends NotFoundException
{
    public static function withId(ProductId $id): self
    {
        return new self(sprintf('Product "%s" not found.', $id));
    }
}
