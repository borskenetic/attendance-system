# CMU migrations

## Active migrations (`2026_05_22_*`)

Fresh schema for new databases — **15 migrations**, no follow-up patches required.

| File | Tables / purpose |
|------|------------------|
| `000001` | `users` |
| `000002` | `password_reset_tokens` |
| `000003` | `sessions` |
| `000004` | `cache`, `cache_locks` |
| `000005` | `jobs`, `job_batches`, `failed_jobs` |
| `000006` | `roles` |
| `000007` | `students` (full patron row: `id_number`, `qrcode`, `normalized_name`, emergency fields, etc.) |
| `000008` | `pending_students` (same patron fields as `students`, minus `qrcode` / system columns) |
| `000009` | `employees` |
| `000010` | `pending_employees` |
| `000011` | `attendance_logs` (`student_id` FK → `students.id`, `section`, `IN`/`OUT`) |
| `000012` | `attendance_feedback` |
| `000013` | `settings` |
| `000015` | `programs` |
| `000016` | `program_years`, `program_courses` |

### Student identifiers

| Column | Meaning |
|--------|---------|
| `id_number` | School ID shown on forms / ID card (e.g. `2024-00001`) |
| `qrcode` | Scanner code (`S-00000001`), generated on register or approval |
| `attendance_logs.student_id` | Internal FK to `students.id` — not the school ID |

There is **no** `student_id` text column on `students` or `pending_students`.

## Retired migrations

Older `2025_*` / `2026_02_*` files are in `_retired/`. Do not run them on a new database.

## Local setup

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan db:seed --class=AdminUserSeeder
php artisan serve
```

Use `migrate:fresh` only on a **new** or **throwaway** database — it drops all tables.

## Notes

- `attendance_logs.status` uses `IN` / `OUT` (matches `AttendanceController`).
- Library tables (books, ebooks, rooms, etc.) are **not** in this set.
- `Setting` model expects the `settings` table (`scan_sms`, etc.).
