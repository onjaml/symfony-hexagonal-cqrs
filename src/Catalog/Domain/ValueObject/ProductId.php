<?php

declare(strict_types=1);

namespace App\Catalog\Domain\ValueObject;

use App\Shared\Domain\Exception\DomainException;

final readonly class ProductId implements \Stringable
{
    private const PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/';

    private function __construct(public string $value)
    {
    }

    public static function fromString(string $value): self
    {
        $value = strtolower($value);

        if (1 !== preg_match(self::PATTERN, $value)) {
            throw new DomainException(sprintf('"%s" is not a valid product id.', $value));
        }

        return new self($value);
    }

    public static function generate(): self
    {
        $milliseconds = (int) floor(microtime(true) * 1000);
        $bytes = pack('J', $milliseconds);
        $bytes = substr($bytes, 2).random_bytes(10);
        $bytes[6] = chr((ord($bytes[6]) & 0x0F) | 0x70);
        $bytes[8] = chr((ord($bytes[8]) & 0x3F) | 0x80);
        $hex = bin2hex($bytes);

        return new self(sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12),
        ));
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
