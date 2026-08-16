# Migrations

Incremental changes applied **on top of** `database/ngo_website.sql`
(the base schema + seed data). Run in filename order after the base
import; each file is idempotent (`CREATE TABLE IF NOT EXISTS`,
`INSERT ... ON DUPLICATE KEY UPDATE`) so re-running it is harmless.

Apply via cPanel/hPanel → phpMyAdmin → select the database → Import →
upload the file, or:
```
mysql -u user -p database_name < database/migrations/001_add_locations.sql
```

**Before running against a live/production database, take a full
database backup first.**

| File | Adds |
|---|---|
| `001_add_locations.sql` | `states`, `districts`, `talukas` tables + complete India states/UTs + districts + Maharashtra talukas (see main report for sourcing and current coverage). |
