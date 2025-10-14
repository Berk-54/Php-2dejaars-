# PHP Login Starter (XAMPP)

**Structuur**
```
php-login-starter/
├── classes/
│   └── User.php        # gebruikt: require_once __DIR__ . '/../db.php';
├── db.php              # centrale PDO-verbinding
├── index.php
├── index_test.php
├── login_form.php
└── register_form.php
```

**Installeren**
1. Plaats de map `php-login-starter` in je XAMPP `htdocs`.
2. Zorg dat je database `Login` bestaat met de juiste tabellen (users e.d.).
3. Pas zo nodig in `db.php` de DB-gegevens aan (DB_HOST, DB_NAME, DB_USER, DB_PASS).
4. Ga naar `http://localhost/php-login-starter/login_form.php` in je browser.

**Waarom dit werkt**
`classes/User.php` gebruikt `require_once __DIR__ . '/../db.php'`.
`__DIR__` is de map van `User.php` (dus `.../classes`). Met `/../db.php` ga je één map omhoog naar de root, waar `db.php` staat.
