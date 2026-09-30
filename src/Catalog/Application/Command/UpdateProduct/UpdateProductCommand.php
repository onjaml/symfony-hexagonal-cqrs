<?php

declare(strict_types=1);

namespace App\Catalog\Application\Command\UpdateProduct;

use App\Shared\Application\Command\CommandInterface;

final readonly class UpdateProductCommand implements CommandInterface
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description,
        public int $price,
        public string $currency,
    ) {
    }
}
