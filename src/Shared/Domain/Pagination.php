<?php

declare(strict_types=1);

namespace App\Shared\Domain;

/**
 * @template T
 */
final readonly class Pagination
{
    /**
     * @param list<T> $items
     */
    public function __construct(
        public array $items,
        public int $page,
        public int $itemsPerPage,
        public int $totalItems,
    ) {
    }

    public function lastPage(): int
    {
        return max(1, (int) ceil($this->totalItems / max(1, $this->itemsPerPage)));
    }
}
