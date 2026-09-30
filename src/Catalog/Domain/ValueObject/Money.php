<?php

declare(strict_types=1);

namespace App\Catalog\Domain\ValueObject;

use App\Shared\Domain\Exception\DomainException;

final readonly class Money
{
    public string $currency;

    public function __construct(
        public int $amount,
        string $currency,
    ) {
        if ($amount < 0) {
            throw new DomainException('Amount cannot be negative.');
        }

        $currency = strtoupper($currency);

        if (1 !== preg_match('/^[A-Z]{3}$/', $currency)) {
            throw new DomainException(sprintf('"%s" is not a valid ISO 4217 currency code.', $currency));
        }

        $this->currency = $currency;
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }
}
