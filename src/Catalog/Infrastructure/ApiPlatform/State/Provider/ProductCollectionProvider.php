<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination as ApiPagination;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\Catalog\Application\Query\FindProducts\FindProductsQuery;
use App\Catalog\Domain\Model\Product;
use App\Catalog\Infrastructure\ApiPlatform\Resource\ProductResource;
use App\Shared\Application\Query\QueryBusInterface;
use App\Shared\Domain\Pagination;

/**
 * @implements ProviderInterface<ProductResource>
 */
final readonly class ProductCollectionProvider implements ProviderInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private ApiPagination $pagination,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): TraversablePaginator
    {
        $page = $this->pagination->getPage($context);
        $itemsPerPage = $this->pagination->getLimit($operation, $context);

        /** @var Pagination<Product> $result */
        $result = $this->queryBus->ask(new FindProductsQuery($page, $itemsPerPage));

        return new TraversablePaginator(
            new \ArrayIterator(array_map(ProductResource::fromModel(...), $result->items)),
            $result->page,
            $result->itemsPerPage,
            $result->totalItems,
        );
    }
}
