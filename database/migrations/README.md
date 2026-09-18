# Database Migrations Directory

Place all future incremental database schema migration files in this directory.

## Naming Convention
Naming format must be: `000X_description.sql`

Examples:
- `0001_baseline.sql` (Initial baseline adoption marker)
- `0002_add_phone_to_user.sql`
- `0003_create_audit_logs_table.sql`

## Safety Guidelines:
1. **Never use `DROP TABLE`, `DROP DATABASE`, `TRUNCATE`, or destructive `DELETE` statements.**
2. Use `ALTER TABLE ... ADD COLUMN ... IF NOT EXISTS` or `CREATE TABLE IF NOT EXISTS` patterns where possible.
3. Test every migration file locally on your development database before committing to GitHub.
4. Execute migrations using the CLI runner:
   ```bash
   php database/migration.php migrate
   ```
