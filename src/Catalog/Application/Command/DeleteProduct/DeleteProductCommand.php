<?php

declare(strict_types=1);

namespace App\Catalog\Application\Command\DeleteProduct;

use App\Shared\Application\Command\CommandInterface;

final readonly class DeleteProductCommand implements CommandInterface
{
    public function __construct(public string $id)
    {
    }
}
