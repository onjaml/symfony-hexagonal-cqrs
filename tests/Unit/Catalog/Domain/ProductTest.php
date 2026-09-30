<?php

declare(strict_types=1);

namespace App\Tests\Unit\Catalog\Domain;

use App\Catalog\Domain\Model\Product;
use App\Catalog\Domain\ValueObject\Money;
use App\Catalog\Domain\ValueObject\ProductId;
use App\Catalog\Domain\ValueObject\ProductName;
use App\Shared\Domain\Exception\DomainException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ProductTest extends TestCase
{
    public function testCreate(): void
    {
        $now = new \DateTimeImmutable('2026-01-01 10:00:00');
        $product = Product::create(ProductId::generate(), new ProductName('  Keyboard  '), '  ', new Money(4999, 'eur'), $now);

        self::assertSame('Keyboard', $product->name()->value);
        self::assertNull($product->description());
        self::assertSame(4999, $product->price()->amount);
        self::assertSame('EUR', $product->price()->currency);
        self::assertEquals($now, $product->createdAt());
        self::assertEquals($now, $product->updatedAt());
    }

    public function testUpdate(): void
    {
        $product = Product::create(ProductId::generate(), new ProductName('Keyboard'), null, new Money(4999, 'EUR'), new \DateTimeImmutable('2026-01-01'));
        $later = new \DateTimeImmutable('2026-02-01');

        $product->update(new ProductName('Mouse'), 'Wireless', new Money(2999, 'USD'), $later);

        self::assertSame('Mouse', $product->name()->value);
        self::assertSame('Wireless', $product->description());
        self::assertTrue($product->price()->equals(new Money(2999, 'USD')));
        self::assertEquals($later, $product->updatedAt());
        self::assertEquals(new \DateTimeImmutable('2026-01-01'), $product->createdAt());
    }

    public function testGeneratedIdIsUuidV7(): void
    {
        $id = ProductId::generate();

        self::assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $id->value);
        self::assertTrue($id->equals(ProductId::fromString(strtoupper($id->value))));
    }

    /**
     * @return iterable<string, array{\Closure}>
     */
    public static function invalidValues(): iterable
    {
        yield 'empty name' => [static fn () => new ProductName('   ')];
        yield 'too long name' => [static fn () => new ProductName(str_repeat('a', 256))];
        yield 'negative amount' => [static fn () => new Money(-1, 'EUR')];
        yield 'invalid currency' => [static fn () => new Money(100, 'EURO')];
        yield 'invalid id' => [static fn () => ProductId::fromString('not-a-uuid')];
    }

    #[DataProvider('invalidValues')]
    public function testInvariants(\Closure $factory): void
    {
        $this->expectException(DomainException::class);

        $factory();
    }
}
