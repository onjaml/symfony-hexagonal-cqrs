<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\Doctrine\Type;

use App\Catalog\Domain\ValueObject\ProductId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

final class ProductIdType extends GuidType
{
    public const NAME = 'product_id';

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?ProductId
    {
        if (null === $value || $value instanceof ProductId) {
            return $value;
        }

        return ProductId::fromString((string) $value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        return $value instanceof ProductId ? $value->value : ProductId::fromString((string) $value)->value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
