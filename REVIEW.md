# Code Review Notes

## Security
- **Session fixation risk**: `Auth::login()` does not regenerate the session ID after authenticating the user. An attacker could pre-set a victim's session ID, have them log in, and then reuse the authenticated session. Call `session_regenerate_id(true)` (and optionally reset the session cookie) before storing user data. 【F:app/services/Auth.php†L43-L72】

## Performance & Maintainability
- **Deprecated pagination pattern**: `/api/jobs` relies on `SQL_CALC_FOUND_ROWS` + `SELECT FOUND_ROWS()` for pagination totals. This hint was deprecated in MySQL 8.0.17 and can be significantly slower on large datasets. Prefer a separate `SELECT COUNT(*) FROM jobs WHERE status = 'published'` query that reuses the same filters as the main select. 【F:routes/web.php†L31-L67】

## Observations / Nice-to-haves
- Consider hardening logout by clearing the session cookie in addition to `session_destroy()` to avoid leaving obsolete identifiers in the browser. 【F:app/services/Auth.php†L74-L79】
- The `.env` loader only feeds values to `getenv()`. If the application ever relies on `$_ENV`/`$_SERVER`, mirror the assignments there as well.
