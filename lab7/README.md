# Peer Tutoring Hub · Lab 7

PHP 8.1 or later with PDO SQLite and the sqlite3 CLI are required. Use fictional test data and keep the Codespaces port private.

Run these commands from `lab7` to set up a new database:

```sh
sqlite3 database/tutoring.db < database/setup-database.sql
sqlite3 database/tutoring.db < database/upgrade-to-user-management.sql
sqlite3 database/tutoring.db '.tables'
php database/create-admin.php "Test Administrator" admin@example.com "<test-password-at-least-12-bytes>"
php -S 0.0.0.0:8000 php/router.php
```

Run both SQL files once for a fresh database. For an existing Lab 6 database, back it up and run only the upgrade script. The router serves application pages and CSS while blocking direct requests to private project paths, including the database. In Codespaces, open the forwarded port 8000 in the Ports panel.

Register a test student from the login page. Students see My Requests and can edit or cancel their own Open requests. Administrators can search and manage accounts and view all tutoring requests. A legacy student profile stays unlinked until an authorized reconciliation process exists. Existing records remain available to administrators.

Run the three suites from a separate terminal:

```sh
php tests/validation-tests.php
php tests/user-management-tests.php
php tests/integration-tests.php
```

Tutor functionality is reserved for a later lab. The delete confirmation checks for dependent requests and blocks permanent deletion pending the course's retention-policy decision.
