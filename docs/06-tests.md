# 06 — Tests

```bash
make test
```

## Stratégie

| Suite | Dossier | Portée | Dépendances |
|---|---|---|---|
| Unitaires domaine | `tests/Unit/Catalog/Domain` | invariants des value objects, cycle de vie de `Product`, format UUID v7 | aucune |
| Unitaires application | `tests/Unit/Catalog/Application` | handlers de commandes et requêtes | `InMemoryProductRepository` |
| Fonctionnels API | `tests/Functional/Catalog` | les 6 opérations HTTP, validation 422, 404 | kernel Symfony, base `app_test` |
| Fixtures | `tests/Functional/Catalog/ProductFixturesTest.php` | chargement des fixtures via le domaine, 50 produits valides exposés par l'API | kernel Symfony, base `app_test` |

Les tests unitaires n'amorcent pas le kernel : ils instancient directement les handlers avec l'adapter mémoire, ce qui valide que la couche Application ne dépend d'aucun framework.

Les tests fonctionnels utilisent `ApiPlatform\Test\ApiTestCase` (paquet `api-platform/test`, API Platform 5) (client HTTP interne, assertions JSON). La table `product` est vidée avant chaque test.

## Lancer une suite

```bash
docker compose exec php php bin/phpunit tests/Unit
docker compose exec php php bin/phpunit tests/Functional
docker compose exec php php bin/phpunit --filter testCrud
```

## Contrôle d'architecture

```bash
grep -rnE "Symfony|Doctrine|ApiPlatform" src/*/Domain src/*/Application
```

La commande ne doit rien renvoyer.
