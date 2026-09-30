<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Catalog\Application\Command\DeleteProduct\DeleteProductCommand;
use App\Catalog\Infrastructure\ApiPlatform\Resource\ProductResource;
use App\Shared\Application\Command\CommandBusInterface;

/**
 * @implements ProcessorInterface<ProductResource, null>
 */
final readonly class DeleteProductProcessor implements ProcessorInterface
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): null
    {
        $this->commandBus->dispatch(new DeleteProductCommand((string) $uriVariables['id']));

        return null;
    }
}
