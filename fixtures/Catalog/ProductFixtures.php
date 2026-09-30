<?php

declare(strict_types=1);

namespace App\Fixtures\Catalog;

use App\Catalog\Application\Command\CreateProduct\CreateProductCommand;
use App\Catalog\Domain\ValueObject\ProductId;
use App\Shared\Application\Command\CommandBusInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

final class ProductFixtures extends Fixture
{
    public const COUNT = 50;
    private const SEED = 20260930;

    private const ITEMS = [
        'Clavier', 'Souris', 'Écran', 'Casque', 'Webcam', 'Microphone', 'Enceinte', 'Tapis de souris',
        'Hub USB-C', 'Chargeur', 'Câble HDMI', 'Disque SSD', 'Clé USB', "Station d'accueil",
        'Lampe de bureau', 'Chaise ergonomique', "Support d'écran", 'Batterie externe',
    ];

    private const QUALIFIERS = [
        'sans fil', 'mécanique', 'ergonomique', '4K', 'Bluetooth', 'Pro', 'compact', 'RGB', 'USB-C', 'silencieux',
    ];

    private const CURRENCIES = ['EUR', 'EUR', 'EUR', 'EUR', 'USD', 'GBP'];

    public function __construct(private readonly CommandBusInterface $commandBus)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->seed(self::SEED);

        for ($i = 0; $i < self::COUNT; ++$i) {
            $item = $faker->randomElement(self::ITEMS);
            $qualifiers = array_values(array_filter(self::QUALIFIERS, static fn (string $q): bool => !str_contains($item, $q)));

            $this->commandBus->dispatch(new CreateProductCommand(
                ProductId::generate()->value,
                sprintf('%s %s %s', $item, $faker->randomElement($qualifiers), strtoupper($faker->bothify('??-###'))),
                $faker->boolean(80) ? $this->description($faker) : null,
                $faker->numberBetween(499, 49_999),
                $faker->randomElement(self::CURRENCIES),
            ));
        }
    }

    private function description(Generator $faker): string
    {
        return sprintf(
            '%s. Garantie %d ans, livré par %s.',
            ucfirst($faker->catchPhrase()),
            $faker->numberBetween(2, 5),
            $faker->company(),
        );
    }
}
