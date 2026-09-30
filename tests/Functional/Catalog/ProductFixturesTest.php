<?php

declare(strict_types=1);

namespace App\Tests\Functional\Catalog;

use ApiPlatform\Test\ApiTestCase;
use App\Fixtures\Catalog\ProductFixtures;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class ProductFixturesTest extends ApiTestCase
{
    protected static ?bool $alwaysBootKernel = true;

    public function testFixturesAreLoadedThroughTheDomain(): void
    {
        $command = (new Application(self::bootKernel()))->find('doctrine:fixtures:load');
        $tester = new CommandTester($command);
        $tester->execute([], ['interactive' => false]);
        $tester->assertCommandIsSuccessful();

        $response = self::createClient()->request('GET', '/api/products', ['query' => ['itemsPerPage' => 100]]);

        self::assertResponseIsSuccessful();
        $data = $response->toArray();
        self::assertSame(ProductFixtures::COUNT, $data['totalItems']);

        foreach ($data['member'] as $product) {
            self::assertNotSame('', $product['name']);
            self::assertGreaterThanOrEqual(0, $product['price']);
            self::assertMatchesRegularExpression('/^[A-Z]{3}$/', $product['currency']);
        }
    }
}
