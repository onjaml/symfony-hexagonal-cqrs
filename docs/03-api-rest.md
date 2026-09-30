# 03 — API REST

Base : `http://api.localhost:8088/api` (ou `https://api.localhost:8443/api`).

Documentation interactive (Swagger UI) : `http://api.localhost:8088/api/docs`
Spécification OpenAPI : `http://api.localhost:8088/api/docs.jsonopenapi`

## Endpoints

| Méthode | Route | Content-Type requête | Succès | Erreurs |
|---|---|---|---|---|
| `GET` | `/api/products` | — | 200 | — |
| `GET` | `/api/products/{id}` | — | 200 | 404 |
| `POST` | `/api/products` | `application/ld+json` ou `application/json` | 201 | 400, 422 |
| `PUT` | `/api/products/{id}` | `application/ld+json` ou `application/json` | 200 | 404, 400, 422 |
| `PATCH` | `/api/products/{id}` | `application/merge-patch+json` | 200 | 404, 400, 422 |
| `DELETE` | `/api/products/{id}` | — | 204 | 404 |

`{id}` doit être un UUID ; toute autre valeur renvoie 404 (contrainte de route).

## Représentation

| Champ | Type | Écriture | Règles |
|---|---|---|---|
| `id` | string (UUID v7) | non | généré par le serveur |
| `name` | string | oui | obligatoire, ≤ 255 |
| `description` | string \| null | oui | vide → `null` ; omis de la réponse quand `null` |
| `price` | integer | oui | obligatoire, ≥ 0, en centimes |
| `currency` | string | oui | code ISO 4217 en majuscules, défaut `EUR` |
| `createdAt` | date-time | non | |
| `updatedAt` | date-time | non | |

Formats de réponse : `application/ld+json` (défaut, Hydra) et `application/json` (via l'en-tête `Accept`).

## Exemples

### Création

```bash
curl -s -X POST http://api.localhost:8088/api/products \
  -H 'Content-Type: application/ld+json' \
  -d '{"name":"Clavier","description":"Mécanique","price":4999,"currency":"EUR"}'
```

```json
{
  "@context": "/api/contexts/Product",
  "@id": "/api/products/0199a1b2-3c4d-7e8f-9a0b-1c2d3e4f5a6b",
  "@type": "Product",
  "id": "0199a1b2-3c4d-7e8f-9a0b-1c2d3e4f5a6b",
  "name": "Clavier",
  "description": "Mécanique",
  "price": 4999,
  "currency": "EUR",
  "createdAt": "2026-09-30T20:00:00+00:00",
  "updatedAt": "2026-09-30T20:00:00+00:00"
}
```

### Modification partielle

```bash
curl -s -X PATCH http://api.localhost:8088/api/products/<id> \
  -H 'Content-Type: application/merge-patch+json' \
  -d '{"price":3999}'
```

### Remplacement complet

```bash
curl -s -X PUT http://api.localhost:8088/api/products/<id> \
  -H 'Content-Type: application/ld+json' \
  -d '{"name":"Souris","description":null,"price":1999,"currency":"USD"}'
```

### Suppression

```bash
curl -s -o /dev/null -w '%{http_code}\n' -X DELETE http://api.localhost:8088/api/products/<id>
```

## Pagination

`GET /api/products?page=2&itemsPerPage=10` (défaut 30 par page, tri `createdAt` décroissant).

```json
{
  "@type": "Collection",
  "totalItems": 42,
  "member": [ ... ],
  "view": {
    "@id": "/api/products?page=2",
    "first": "/api/products?page=1",
    "last": "/api/products?page=5",
    "previous": "/api/products?page=1",
    "next": "/api/products?page=3"
  }
}
```

Le client peut choisir la taille de page (`pagination_client_items_per_page`), plafonnée à 100.

## Erreurs

Les erreurs suivent la RFC 7807 (`application/problem+json`) ou le format Hydra selon l'en-tête `Accept`.

| Origine | Code |
|---|---|
| Corps JSON mal formé | 400 |
| Type incorrect (ex. `"price": "abc"`) | 422 |
| `ProductNotFound` (domaine) ou produit absent au provider | 404 |
| Contraintes Symfony Validator sur `ProductResource` | 422 (`ConstraintViolation`) |
| `Shared\Domain\Exception\DomainException` (invariant métier) | 422 |

Le mapping exception → statut est déclaré dans `config/packages/api_platform.yaml` (`exception_to_status`).
