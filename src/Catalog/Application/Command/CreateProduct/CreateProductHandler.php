<?php

declare(strict_types=1);

namespace App\Catalog\Application\Command\CreateProduct;

use App\Catalog\Domain\Model\Product;
use App\Catalog\Domain\Repository\ProductRepositoryInterface;
use App\Catalog\Domain\ValueObject\Money;
use App\Catalog\Domain\ValueObject\ProductId;
use App\Catalog\Domain\ValueObject\ProductName;
use App\Shared\Application\Command\CommandHandlerInterface;

final readonly class CreateProductHandler implements CommandHandlerInterface
{
    public function __construct(private ProductRepositoryInterface $products)
    {
    }

    public function __invoke(CreateProductCommand $command): void
    {
        $this->products->save(Product::create(
            ProductId::fromString($command->id),
            new ProductName($command->name),
            $command->description,
            new Money($command->price, $command->currency),
        ));
    }
}
