# 04 — Persistance

## Mapping Doctrine en XML

Le domaine ne porte aucun attribut Doctrine. Le mapping est déclaré en XML dans `src/Catalog/Infrastructure/Doctrine/Mapping/` et chargé par `config/packages/doctrine.yaml` :

```yaml
mappings:
    Catalog:
        type: xml
        is_bundle: false
        dir: '%kernel.project_dir%/src/Catalog/Infrastructure/Doctrine/Mapping'
        prefix: 'App\Catalog\Domain'
```

Doctrine utilise le `SimplifiedXmlDriver` : le nom de fichier est le nom de classe relatif au préfixe `App\Catalog\Domain`, les `\` étant remplacés par des `.`, suffixé de `.orm.xml`.

| Fichier | Classe | Rôle |
|---|---|---|
| `Model.Product.orm.xml` | `Domain\Model\Product` | entité, table `product` |
| `ValueObject.ProductName.orm.xml` | `Domain\ValueObject\ProductName` | embeddable → colonne `name` |
| `ValueObject.Money.orm.xml` | `Domain\ValueObject\Money` | embeddable → colonnes `price_amount`, `price_currency` |

Vérification : `make console c="doctrine:mapping:info"` et `make console c="doctrine:schema:validate"`.

## Types personnalisés

`ProductIdType` (`product_id`) étend `GuidType` : colonne native `UUID` sous PostgreSQL, conversion transparente vers/depuis `ProductId`. Enregistré dans `doctrine.yaml` :

```yaml
dbal:
    types:
        product_id: App\Catalog\Infrastructure\Doctrine\Type\ProductIdType
```

## Schéma

| Colonne | Type SQL | Source |
|---|---|---|
| `id` | `UUID` PK | `ProductId` |
| `name` | `VARCHAR(255)` | `ProductName` |
| `description` | `TEXT NULL` | |
| `price_amount` | `INT` | `Money::amount` |
| `price_currency` | `VARCHAR(3)` | `Money::currency` |
| `created_at` | `TIMESTAMP(0)` | |
| `updated_at` | `TIMESTAMP(0)` | |

## Migrations

```bash
make console c="doctrine:migrations:diff"
make migrate
```

Les migrations sont versionnées dans `migrations/`. En test, `make db-test` crée la base `app_test` et y joue les migrations.

## Repository

`DoctrineProductRepository` implémente le port `ProductRepositoryInterface` :
- `save()` / `remove()` : `persist`/`remove` + `flush` (encapsulés dans la transaction du `command.bus`) ;
- `ofId()` : `EntityManager::find()` ;
- `paginate()` : `Doctrine\ORM\Tools\Pagination\Paginator`, tri `createdAt DESC, id DESC`.
