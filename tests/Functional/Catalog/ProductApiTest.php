<?php

declare(strict_types=1);

namespace App\Tests\Functional\Catalog;

use ApiPlatform\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;

final class ProductApiTest extends ApiTestCase
{
    protected static ?bool $alwaysBootKernel = true;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        self::getContainer()->get(EntityManagerInterface::class)->getConnection()->executeStatement('TRUNCATE product');
    }

    public function testCrud(): void
    {
        $client = self::createClient();

        $response = $client->request('POST', '/api/products', ['json' => [
            'name' => 'Keyboard',
            'description' => 'Mechanical',
            'price' => 4999,
            'currency' => 'EUR',
        ]]);
        self::assertResponseStatusCodeSame(201);
        self::assertJsonContains(['@type' => 'Product', 'name' => 'Keyboard', 'price' => 4999]);
        $iri = $response->toArray()['@id'];
        self::assertMatchesRegularExpression('#^/api/products/[0-9a-f-]{36}$#', $iri);

        $client->request('GET', $iri);
        self::assertResponseIsSuccessful();
        self::assertJsonContains(['name' => 'Keyboard', 'currency' => 'EUR']);

        $client->request('GET', '/api/products');
        self::assertResponseIsSuccessful();
        self::assertJsonContains(['totalItems' => 1]);

        $client->request('PATCH', $iri, [
            'json' => ['price' => 3999],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);
        self::assertResponseIsSuccessful();
        self::assertJsonContains(['name' => 'Keyboard', 'price' => 3999]);

        $client->request('PUT', $iri, ['json' => [
            'name' => 'Mouse',
            'description' => null,
            'price' => 1999,
            'currency' => 'USD',
        ]]);
        self::assertResponseIsSuccessful();
        self::assertJsonContains(['name' => 'Mouse', 'price' => 1999, 'currency' => 'USD']);

        $client->request('DELETE', $iri);
        self::assertResponseStatusCodeSame(204);

        $client->request('GET', $iri);
        self::assertResponseStatusCodeSame(404);
    }

    public function testValidation(): void
    {
        self::createClient()->request('POST', '/api/products', ['json' => [
            'name' => '',
            'price' => -10,
            'currency' => 'XXXX',
        ]]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains(['@type' => 'ConstraintViolation']);
    }

    public function testUnknownProduct(): void
    {
        self::createClient()->request('GET', '/api/products/0199a000-0000-7000-8000-000000000000');

        self::assertResponseStatusCodeSame(404);
    }
}
