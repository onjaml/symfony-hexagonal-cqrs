<?php

declare(strict_types=1);

namespace App\Catalog\Domain\ValueObject;

use App\Shared\Domain\Exception\DomainException;

final readonly class ProductName
{
    public const MAX_LENGTH = 255;

    public string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ('' === $value) {
            throw new DomainException('Product name cannot be empty.');
        }

        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw new DomainException(sprintf('Product name cannot exceed %d characters.', self::MAX_LENGTH));
        }

        $this->value = $value;
    }
}
