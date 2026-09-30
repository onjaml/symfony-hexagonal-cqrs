<?php

declare(strict_types=1);

namespace App\Catalog\Domain\Model;

use App\Catalog\Domain\ValueObject\Money;
use App\Catalog\Domain\ValueObject\ProductId;
use App\Catalog\Domain\ValueObject\ProductName;

class Product
{
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    private function __construct(
        private ProductId $id,
        private ProductName $name,
        private ?string $description,
        private Money $price,
        \DateTimeImmutable $now,
    ) {
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public static function create(
        ProductId $id,
        ProductName $name,
        ?string $description,
        Money $price,
        \DateTimeImmutable $now = new \DateTimeImmutable(),
    ): self {
        return new self($id, $name, self::normalizeDescription($description), $price, $now);
    }

    public function update(
        ProductName $name,
        ?string $description,
        Money $price,
        \DateTimeImmutable $now = new \DateTimeImmutable(),
    ): void {
        $this->name = $name;
        $this->description = self::normalizeDescription($description);
        $this->price = $price;
        $this->updatedAt = $now;
    }

    public function id(): ProductId
    {
        return $this->id;
    }

    public function name(): ProductName
    {
        return $this->name;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function price(): Money
    {
        return $this->price;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private static function normalizeDescription(?string $description): ?string
    {
        $description = null === $description ? null : trim($description);

        return '' === $description ? null : $description;
    }
}
