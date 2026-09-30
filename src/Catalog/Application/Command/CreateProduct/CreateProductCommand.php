<?php

declare(strict_types=1);

namespace App\Catalog\Application\Command\CreateProduct;

use App\Shared\Application\Command\CommandInterface;

final readonly class CreateProductCommand implements CommandInterface
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
