<?php

declare(strict_types=1);

namespace App\Catalog\Application\Command\UpdateProduct;

use App\Catalog\Domain\Exception\ProductNotFound;
use App\Catalog\Domain\Repository\ProductRepositoryInterface;
use App\Catalog\Domain\ValueObject\Money;
use App\Catalog\Domain\ValueObject\ProductId;
use App\Catalog\Domain\ValueObject\ProductName;
use App\Shared\Application\Command\CommandHandlerInterface;

final readonly class UpdateProductHandler implements CommandHandlerInterface
{
    public function __construct(private ProductRepositoryInterface $products)
    {
    }

    public function __invoke(UpdateProductCommand $command): void
    {
        $id = ProductId::fromString($command->id);
        $product = $this->products->ofId($id) ?? throw ProductNotFound::withId($id);

        $product->update(
            new ProductName($command->name),
            $command->description,
            new Money($command->price, $command->currency),
        );

        $this->products->save($product);
    }
}
