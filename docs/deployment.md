# Deployment LevelUp Market

Deployed 16 September 2026 to `root@72.60.194.28` (srv1008845).

- Public site: https://levelupgamehub.com/
- `http` and `www` redirect to the canonical HTTPS origin.
- Active symlink: `/var/www/levelupgamehub/current`
- Release: `/var/www/levelupgamehub/releases/20260916-190511`
- Shared environment/storage: `/var/www/levelupgamehub/shared`
- Runtime: PHP 8.3 FPM, Nginx; Composer lock installed with `--no-dev`.
- Database: existing **host MySQL**, `127.0.0.1:3306`, `lavelupgaminghub`. This is separate from the Docker MySQL service on port 3307.
- Dedicated application database user: `levelup_app`; credentials remain only in the protected server environment.

## Data and recovery

Verified pre-migration database backup:
`/var/backups/levelupgamehub/20260916-190511/database-before.sql.gz`.

The existing database contained 62 legacy games, 17 vouchers and zero reviews. The pending migrations replaced the legacy catalog tables; the original rows remain in that backup. No demo seeder was run in production. Saweria populated 110 active catalog entries. Existing user/review tables were preserved.

The deployment used a working-tree snapshot; the source was subsequently prepared for publication to `bintangjtobing/lavelupgamehub`. The final validated `SaweriaClient.php` and scheduler were copied into the staged release before enabling cron. Preserve this release before future deployments. Database rollback requires a deliberate restore and reconciliation of any newer production writes; the legacy drop migration cannot restore old tables through `migrate:rollback`.

## Automatic updates

`/etc/cron.d/levelupgamehub` runs Laravel's scheduler every minute as `www-data`.
`php artisan saweria:sync --prune` runs every 15 minutes with overlap protection.
Logs: `shared/storage/logs/saweria-sync.log` and `scheduler.log`.

Catalog responses must pass pagination, count, item-shape and duplicate checks before sync returns data for pruning. A failed/incomplete response does not cause missing products to be marked inactive. The sync command refuses an entirely empty response. Individual game prices and denominations are fetched directly on each product-page request, separately from catalog scheduling.

## TLS

Certificate covers `levelupgamehub.com` and `www.levelupgamehub.com`, initially expires 15 December 2026. Certbot timer is enabled; the certificate-specific renewal deploy hook reloads Nginx. ACME webroot: `/var/www/levelupgamehub/acme`.

## Verification

- Nine public HTML pages return HTTPS 200, valid JSON-LD and one H1.
- Fresh Mobile Legends product options available from Saweria.
- Live tracking POST successfully read the existing sample order with CSRF/session validation, no-store and noindex headers; no payment created.
- Sitemap contains 118 URLs; robots, llms, platform icons and manifest return 200.
- GA4 measurement ID is `G-ZMQ2EZKSBP`; tracking page excludes analytics.
- Mobile layout has no horizontal overflow; `.env` and `.git/config` return 403/404.
- Local suite: 45 tests, 317 assertions. Local SQLite checksum unchanged.
- Browser QA did not submit payments, contact messages, or reviews. GA4 dashboard receipt and SMTP delivery were not tested.

## Monitoring and first scheduled run

Uptime Kuma monitor **88**, **LevelUp Market**, checks `https://levelupgamehub.com/` every 60 seconds with TLS verification enabled. Public group: **Aplikasi Bintang**, tag **Bintang**. Status page: https://status.bintangtobing.com/status/bintangtobing . Public heartbeats were UP at 19:14 and 19:15 WIB (113 ms and 100 ms). No notification channels were added.

Kuma backup: `/var/backups/uptime-kuma/20260916-levelup/kuma.db`.

The actual system cron invoked the scheduler at **19:15:01 WIB** on 16 September 2026; catalog sync completed successfully in 2.255 seconds and updated all 110 active products. This verifies automatic operation, not only a manual command run.
