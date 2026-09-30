<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Catalog\Domain\Model\Product;
use App\Catalog\Domain\ValueObject\ProductName;
use App\Catalog\Infrastructure\ApiPlatform\State\Processor\CreateProductProcessor;
use App\Catalog\Infrastructure\ApiPlatform\State\Processor\DeleteProductProcessor;
use App\Catalog\Infrastructure\ApiPlatform\State\Processor\UpdateProductProcessor;
use App\Catalog\Infrastructure\ApiPlatform\State\Provider\ProductCollectionProvider;
use App\Catalog\Infrastructure\ApiPlatform\State\Provider\ProductItemProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'Product',
    operations: [
        new GetCollection(provider: ProductCollectionProvider::class),
        new Get(requirements: self::ID_REQUIREMENT, provider: ProductItemProvider::class),
        new Post(processor: CreateProductProcessor::class),
        new Put(requirements: self::ID_REQUIREMENT, provider: ProductItemProvider::class, processor: UpdateProductProcessor::class),
        new Patch(requirements: self::ID_REQUIREMENT, provider: ProductItemProvider::class, processor: UpdateProductProcessor::class),
        new Delete(requirements: self::ID_REQUIREMENT, provider: ProductItemProvider::class, processor: DeleteProductProcessor::class),
    ],
    paginationItemsPerPage: 30,
)]
final class ProductResource
{
    private const ID_REQUIREMENT = ['id' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}'];

    #[ApiProperty(identifier: true, writable: false)]
    public ?string $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: ProductName::MAX_LENGTH)]
    public ?string $name = null;

    public ?string $description = null;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[ApiProperty(description: 'Price in minor units (cents)', example: 1999)]
    public ?int $price = null;

    #[Assert\NotBlank]
    #[Assert\Currency]
    #[ApiProperty(example: 'EUR')]
    public ?string $currency = 'EUR';

    #[ApiProperty(writable: false)]
    public ?\DateTimeImmutable $createdAt = null;

    #[ApiProperty(writable: false)]
    public ?\DateTimeImmutable $updatedAt = null;

    public static function fromModel(Product $product): self
    {
        $resource = new self();
        $resource->id = $product->id()->value;
        $resource->name = $product->name()->value;
        $resource->description = $product->description();
        $resource->price = $product->price()->amount;
        $resource->currency = $product->price()->currency;
        $resource->createdAt = $product->createdAt();
        $resource->updatedAt = $product->updatedAt();

        return $resource;
    }
}
