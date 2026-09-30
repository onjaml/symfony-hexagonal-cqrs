# symfony-hexa-cqrs

CRUD REST `Product` — Symfony 7.4, API Platform 5, architecture hexagonale, CQRS (Symfony Messenger), PostgreSQL 17, FrankenPHP, Traefik v3.

## Démarrage

```bash
make setup
```

| URL | |
|---|---|
| http://api.localhost:8088/api | API (HTTP) |
| https://api.localhost:8443/api | API (HTTPS auto-signé) |
| http://api.localhost:8088/api/docs | Swagger UI |
| http://localhost:8090 | Dashboard Traefik |

Charge 50 produits de démonstration (`make fixtures` pour recharger).

```bash
make test
make help
```

## Documentation technique

1. [Architecture hexagonale](docs/01-architecture-hexagonale.md)
2. [CQRS](docs/02-cqrs.md)
3. [API REST](docs/03-api-rest.md)
4. [Persistance](docs/04-persistence.md)
5. [Docker et Traefik](docs/05-docker-traefik.md)
6. [Tests](docs/06-tests.md)
7. [Ajouter une ressource](docs/07-ajouter-une-ressource.md)
8. [Fixtures](docs/08-fixtures.md)
