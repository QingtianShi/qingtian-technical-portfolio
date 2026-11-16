# AirAsia Rewards Gift Card Management

HW3 manages the Gift Card catalog in PHP, PDO, and SQLite.

## Run in Codespaces

From the project root, check for PHP 8.1+ and the PDO SQLite extension, then start the development server.

```sh
php -v
php -m | grep -i pdo_sqlite
php -S 0.0.0.0:8000 router.php
```

Open forwarded port 8000 in the browser. Keep the forwarded port private. The project includes a seeded database. To build a fresh copy in a different file:

```sh
sqlite3 /tmp/airasia-hw3-rebuilt.db < database/schema.sql
sqlite3 /tmp/airasia-hw3-rebuilt.db < database/seed.sql
sqlite3 database/rewards.db 'PRAGMA integrity_check; SELECT COUNT(*) FROM ACCOUNT; SELECT COUNT(*) FROM GIFTCARD;'
```

Run the validation harness with `php tests/validation-tests.php` or open `/tests/validation-tests.php` in the forwarded browser.

## Requirements and implementation decisions

The Air Asia Web Application Requirements Document (updated February 13, 2019) defines ACCOUNT as `accountId`, `userId`, `accountType`, and `points`, and GIFTCARD as `cardId`, `cardName`, `cardType`, `cardValue`, and `points`. The USER table supports the ACCOUNT foreign key with synthetic seed users. The PHP application implements only gift card management.

The requirements define `cardType` as required text of up to 100 characters and provide no list of allowed values. The HW3 handout requires an allowlist, so this implementation uses Dining, Shopping, and Travel as demo categories. It uses shared placeholder gift card artwork because the HW2 files were unavailable. The HW3-specific workflow displays the detail page after update and requires a confirmation page and POST for deletion.

## Use Case Traceability

| Use case | PHP page | Database table | Test evidence |
|---|---|---|---|
| View a List of Gift Cards | card-list.php | GIFTCARD | T02 checks the seeded card list and its details links. |
| View Details of One Gift Card | card-details.php | GIFTCARD | T03 checks the fields and links on a selected card. |
| Add data for a new gift card | card-add.php | GIFTCARD | T04 checks that a valid submission appears on the list. |
| Update data for one gift card | card-update.php | GIFTCARD | T05 checks prepopulation and confirms the edited data after saving. |
| Delete a card | card-delete.php | GIFTCARD | T06 checks that the confirmation page preserves the row and that confirmed deletion removes it. |

See [test-report.md](test-report.md) for steps, observations, and verification status.
