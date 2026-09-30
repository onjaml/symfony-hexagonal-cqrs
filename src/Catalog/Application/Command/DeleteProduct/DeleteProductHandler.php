<?php

declare(strict_types=1);

namespace App\Catalog\Application\Command\DeleteProduct;

use App\Catalog\Domain\Exception\ProductNotFound;
use App\Catalog\Domain\Repository\ProductRepositoryInterface;
use App\Catalog\Domain\ValueObject\ProductId;
use App\Shared\Application\Command\CommandHandlerInterface;

final readonly class DeleteProductHandler implements CommandHandlerInterface
{
    public function __construct(private ProductRepositoryInterface $products)
    {
    }

    public function __invoke(DeleteProductCommand $command): void
    {
        $id = ProductId::fromString($command->id);
        $product = $this->products->ofId($id) ?? throw ProductNotFound::withId($id);

        $this->products->remove($product);
    }
}
