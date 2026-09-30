<?php

declare(strict_types=1);

namespace App\Tests\Unit\Catalog\Application;

use App\Catalog\Application\Command\CreateProduct\CreateProductCommand;
use App\Catalog\Application\Command\CreateProduct\CreateProductHandler;
use App\Catalog\Application\Command\DeleteProduct\DeleteProductCommand;
use App\Catalog\Application\Command\DeleteProduct\DeleteProductHandler;
use App\Catalog\Application\Command\UpdateProduct\UpdateProductCommand;
use App\Catalog\Application\Command\UpdateProduct\UpdateProductHandler;
use App\Catalog\Application\Query\FindProduct\FindProductHandler;
use App\Catalog\Application\Query\FindProduct\FindProductQuery;
use App\Catalog\Application\Query\FindProducts\FindProductsHandler;
use App\Catalog\Application\Query\FindProducts\FindProductsQuery;
use App\Catalog\Domain\Exception\ProductNotFound;
use App\Catalog\Domain\ValueObject\ProductId;
use App\Catalog\Infrastructure\InMemory\InMemoryProductRepository;
use PHPUnit\Framework\TestCase;

final class ProductHandlersTest extends TestCase
{
    private InMemoryProductRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new InMemoryProductRepository();
    }

    public function testFullLifecycle(): void
    {
        $id = ProductId::generate()->value;

        (new CreateProductHandler($this->repository))(new CreateProductCommand($id, 'Keyboard', null, 4999, 'EUR'));
        $product = (new FindProductHandler($this->repository))(new FindProductQuery($id));
        self::assertSame('Keyboard', $product->name()->value);

        (new UpdateProductHandler($this->repository))(new UpdateProductCommand($id, 'Mouse', 'Wireless', 2999, 'EUR'));
        $product = (new FindProductHandler($this->repository))(new FindProductQuery($id));
        self::assertSame('Mouse', $product->name()->value);
        self::assertSame(2999, $product->price()->amount);

        (new DeleteProductHandler($this->repository))(new DeleteProductCommand($id));
        $this->expectException(ProductNotFound::class);
        (new FindProductHandler($this->repository))(new FindProductQuery($id));
    }

    public function testPaginate(): void
    {
        $create = new CreateProductHandler($this->repository);
        foreach (range(1, 5) as $i) {
            $create(new CreateProductCommand(ProductId::generate()->value, "Product $i", null, $i * 100, 'EUR'));
        }

        $page = (new FindProductsHandler($this->repository))(new FindProductsQuery(page: 2, itemsPerPage: 2));

        self::assertCount(2, $page->items);
        self::assertSame(5, $page->totalItems);
        self::assertSame(3, $page->lastPage());
    }

    public function testUpdateUnknownProduct(): void
    {
        $this->expectException(ProductNotFound::class);

        (new UpdateProductHandler($this->repository))(new UpdateProductCommand(ProductId::generate()->value, 'X', null, 1, 'EUR'));
    }
}
