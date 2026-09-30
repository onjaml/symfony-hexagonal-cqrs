<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Catalog\Application\Query\FindProduct\FindProductQuery;
use App\Catalog\Domain\Exception\ProductNotFound;
use App\Catalog\Domain\Model\Product;
use App\Catalog\Infrastructure\ApiPlatform\Resource\ProductResource;
use App\Shared\Application\Query\QueryBusInterface;

/**
 * @implements ProviderInterface<ProductResource>
 */
final readonly class ProductItemProvider implements ProviderInterface
{
    public function __construct(private QueryBusInterface $queryBus)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?ProductResource
    {
        try {
            /** @var Product $product */
            $product = $this->queryBus->ask(new FindProductQuery((string) $uriVariables['id']));
        } catch (ProductNotFound) {
            return null;
        }

        return ProductResource::fromModel($product);
    }
}
