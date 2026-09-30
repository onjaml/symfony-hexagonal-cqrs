# 07 — Ajouter une ressource

Exemple : un contexte `Ordering` avec une ressource `Order`.

## Domaine

1. `src/Ordering/Domain/ValueObject/OrderId.php` (même modèle que `ProductId`).
2. Autres value objects avec validation dans le constructeur, levant `Shared\Domain\Exception\DomainException`.
3. `src/Ordering/Domain/Model/Order.php` : constructeur privé, fabrique statique, méthodes métier.
4. `src/Ordering/Domain/Repository/OrderRepositoryInterface.php`.
5. `src/Ordering/Domain/Exception/OrderNotFound.php` étendant `NotFoundException`.

## Application

6. Une paire `Command` / `Handler` par écriture, une paire `Query` / `Handler` par lecture, implémentant les interfaces de `src/Shared/Application`.

## Infrastructure

7. `Doctrine/Type/OrderIdType.php`, fichiers `Doctrine/Mapping/*.orm.xml`.
8. `Doctrine/DoctrineOrderRepository.php` et `InMemory/InMemoryOrderRepository.php`.
9. `ApiPlatform/Resource/OrderResource.php` avec `#[ApiResource]`, providers et processors appelant les bus.

## Configuration

10. `config/packages/doctrine.yaml` : ajouter le type et le mapping XML du contexte.
11. `config/packages/api_platform.yaml` : ajouter `src/Ordering/Infrastructure/ApiPlatform/Resource` à `mapping.paths` et les exceptions à `exception_to_status`.
12. `config/services.yaml` : alias `OrderRepositoryInterface` → `DoctrineOrderRepository`, exclusion du dossier `Domain/Model`.

## Finalisation

13. `make console c="doctrine:migrations:diff"` puis `make migrate`.
14. Fixtures dans `fixtures/<Contexte>/` via le bus de commandes (voir [08 — Fixtures](08-fixtures.md)).
15. Tests unitaires (domaine + handlers) et fonctionnels (API).
16. Vérifier : `make console c="debug:messenger"`, `make console c="debug:router"`, Swagger UI.
