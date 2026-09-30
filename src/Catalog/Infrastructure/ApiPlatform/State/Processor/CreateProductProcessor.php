<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Catalog\Application\Command\CreateProduct\CreateProductCommand;
use App\Catalog\Application\Query\FindProduct\FindProductQuery;
use App\Catalog\Domain\ValueObject\ProductId;
use App\Catalog\Infrastructure\ApiPlatform\Resource\ProductResource;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Query\QueryBusInterface;

/**
 * @implements ProcessorInterface<ProductResource, ProductResource>
 */
final readonly class CreateProductProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ProductResource
    {
        \assert($data instanceof ProductResource);

        $id = ProductId::generate()->value;

        $this->commandBus->dispatch(new CreateProductCommand(
            $id,
            (string) $data->name,
            $data->description,
            (int) $data->price,
            (string) $data->currency,
        ));

        return ProductResource::fromModel($this->queryBus->ask(new FindProductQuery($id)));
    }
}
