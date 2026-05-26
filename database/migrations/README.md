# CMU migrations

## Active migrations (`2026_05_22_*`)

**15 migrations** — full schema on first `migrate` / `migrate:fresh` (no rename patches).

| File | Tables / purpose |
|------|------------------|
| `000001` | `users` |
| `000002` | `password_reset_tokens` |
| `000003` | `sessions` |
| `000004` | `cache`, `cache_locks` |
| `000005` | `jobs`, `job_batches`, `failed_jobs` |
| `000006` | `roles` |
| `000007` | `students` |
| `000008` | `pending_students` |
| `000009` | `employees` |
| `000010` | `pending_employees` |
| `000011` | `attendance_logs` |
| `000012` | `attendance_feedback` |
| `000013` | `settings` |
| `000015` | `programs` |
| `000016` | `program_years`, `program_courses` |

### `students` / `pending_students` (patron fields)

Both use the same patron columns in the same order:

`student_id`, `firstname`, `lastname`, `middle_initial`, `birth_date`, `blood_type`, `course`, `year`, `mobile_number`, `profile_picture`, emergency fields, `student_signature`, `address`.

`students` also has: `user_id`, `role_id`, `normalized_name`, `qrcode` (unique scan code).

`pending_students` has no `qrcode` — assigned when a registration is approved.

### Identifier names

| Column | Table | Meaning |
|--------|-------|---------|
| `student_id` | `students`, `pending_students` | School ID (e.g. `2024-00001`) |
| `qrcode` | `students` | Scanner code (`S-00000001`) |
| `student_id` | `attendance_logs` | FK to `students.id` (internal row id) |

## Retired migrations

Older `2025_*` files are in `_retired/`. Do not run them on a new database.

## Local setup

```bash
php artisan migrate:fresh --seed
php artisan db:seed --class=AdminUserSeeder
```

Use `migrate:fresh` only on a **new** or **throwaway** database.
