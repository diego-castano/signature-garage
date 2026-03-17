# Signature Garage - Site Guide

Technical documentation for developers working on the Signature Garage WordPress site.

## Repository Structure

```
wordpress-website/
├── deploy.py                          # FTP deployment script
├── .env                               # FTP credentials (not in git)
├── docs/                              # Documentation
├── wp-content/
│   ├── plugins/
│   │   └── signature-garage-upgrades/ # Custom plugin (all our code)
│   │       ├── signature-garage-upgrades.php   # Main plugin file
│   │       ├── includes/
│   │       │   ├── class-sgu-image-optimizer.php   # Core image optimization (GD)
│   │       │   ├── class-sgu-image-hooks.php       # Auto-optimize on upload
│   │       │   ├── class-sgu-image-bulk.php         # Bulk optimizer + WebP regeneration
│   │       │   ├── class-sgu-frontend-speed.php     # Lazy load, CSS defer, preconnect
│   │       │   ├── class-sgu-dashboard.php          # Custom admin dashboard
│   │       │   ├── class-sgu-analytics.php          # Pageview tracking
│   │       │   ├── class-sgu-no-residentes.php      # NO RES toggle system
│   │       │   ├── class-sgu-admin-organizer.php    # Admin sidebar reorganization
│   │       │   ├── class-sgu-admin.php              # Admin pages + settings
│   │       │   └── class-sgu-logger.php             # Internal logging
│   │       └── admin/
│   │           ├── views/
│   │           │   ├── admin-page.php              # Plugin settings page
│   │           │   ├── tab-image-optimizer.php      # Image optimizer tab
│   │           │   └── tab-logs.php                 # Logs viewer tab
│   │           ├── css/
│   │           │   └── sgu-dashboard.css            # Dashboard styles
│   │           └── js/
│   │               └── sgu-dashboard.js             # Dashboard JS (vehicles table, charts)
│   └── themes/
│       └── signaturecar/              # Active theme
│           └── assets/img/            # Theme images (optimized + WebP)
```

## Deployment

The site is deployed via FTP using `deploy.py`. Credentials are stored in `.env`.

```bash
# Deploy plugin only
python3 deploy.py plugin

# Deploy theme only
python3 deploy.py theme

# Deploy both
python3 deploy.py

# Preview what would be uploaded (no actual upload)
python3 deploy.py plugin --dry-run
```

### .env format

```
FTP_HOST=ftp.signature-garage.com
FTP_USER=your_user
FTP_PASS=your_password
```

The script uploads all files recursively, creating remote directories as needed. It ignores `.DS_Store`, `Thumbs.db`, `.git`, `__pycache__`, and `error_log`.

## Plugin Modules

### Image Optimizer (`class-sgu-image-optimizer.php`)

Core image processing using PHP GD. Handles:

- **Resize**: Images wider than `max_width` (default 1920px) are resized proportionally.
- **Compression**: JPEG quality 82, PNG compression 6, WebP quality 82 (all configurable).
- **WebP generation**: Creates `.webp` sibling file for every optimized image.
- **Transparency**: Preserves PNG/GIF transparency during processing.

**WebP naming convention**: The WebP file is created by *appending* `.webp` to the full filename:
```
image.jpg      → image.jpg.webp
image.png      → image.png.webp
photo-300x200.jpg → photo-300x200.jpg.webp
```

This matches the Apache rewrite rules that look for `%{REQUEST_FILENAME}.webp`.

Default settings (changeable in admin UI):
```php
'max_width'       => 1920,
'jpeg_quality'    => 82,
'png_compression' => 6,
'webp_quality'    => 82,
'auto_optimize'   => true,
```

### Image Hooks (`class-sgu-image-hooks.php`)

Hooks into WordPress upload flow for automatic optimization:

1. `wp_handle_upload` — Optimizes the original file immediately on upload.
2. `wp_generate_attachment_metadata` — Creates WebP for main file + optimizes all thumbnails + creates their WebP versions.
3. `delete_attachment` — Cleans up WebP files when an attachment is deleted.

Also maintains global stats in `sgu_optimizer_global_stats` option.

### Image Bulk Optimizer (`class-sgu-image-bulk.php`)

AJAX endpoints for the admin Image Optimizer tab:

| Endpoint | Action |
|----------|--------|
| `sgu_scan_unoptimized` | Returns IDs of all unoptimized attachments |
| `sgu_optimize_single` | Optimizes one attachment (main + thumbs + WebP) |
| `sgu_get_stats` | Returns global optimization statistics |
| `sgu_save_settings` | Saves optimizer settings |
| `sgu_regenerate_webp_batch` | Batch-regenerates WebP files with correct naming (deletes old-style, creates new-style) |

The `regenerate_webp_batch` endpoint processes 5 images per request. The frontend calls it repeatedly with increasing `offset` until `done: true`.

### Frontend Speed (`class-sgu-frontend-speed.php`)

Performance optimizations applied to the frontend (skipped on admin pages):

**Lazy Loading** — Uses output buffering (`ob_start`) to process ALL HTML output:
- First `<img>` tag gets `fetchpriority="high"` (LCP/hero image)
- All subsequent `<img>` tags get `loading="lazy"` and `decoding="async"`
- Works on ALL images, not just WordPress-filtered content (catches theme hardcoded images too)

**CSS Deferral** — Converts 6 non-critical CSS files to preload/onload pattern:
- `magnific`, `slick`, `slick-theme`, `lity-css`, `fontawesome`, `sbi-styles`
- Original `<link>` kept inside `<noscript>` for fallback

**Resource Hints** — Preconnect + DNS prefetch for:
- `cdnjs.cloudflare.com`
- `www.googletagmanager.com`
- `connect.facebook.net`

**Emoji Removal** — Removes WordPress emoji detection scripts and styles (~45KB saved).

### Dashboard (`class-sgu-dashboard.php`)

Replaces the default WordPress dashboard with a custom layout:

- **Contact Forms**: Shows recent Gravity Forms entries with automatic spam detection, reply/dismiss/spam actions. Integrates with GF entry status.
- **Recent Vehicles**: Shows latest 10 published vehicles (posts + defenders CPT) with thumbnails, prices, NO RES badges, and quick actions.
- **Analytics**: KPI cards (today/week/month views), Chart.js line chart, top pages, traffic sources.
- **Inventory Table**: Full vehicle list with search, filters (type/status/sold), pagination. Loaded via AJAX (`sgu_get_vehicles`).

AJAX endpoints:
| Endpoint | Action |
|----------|--------|
| `sgu_get_vehicles` | Paginated vehicle list with filters |
| `sgu_vehicle_set_draft` | Set a vehicle to draft status |
| `sgu_dismiss_entry` | Dismiss a contact form entry |
| `sgu_mark_spam` | Mark entry as spam (also updates GF) |
| `sgu_toggle_replied` | Toggle replied status on entry |

### Analytics (`class-sgu-analytics.php`)

Lightweight pageview tracker:

- Tracks URL + referrer + timestamp in `wp_sgu_pageviews` table.
- Skips: admin pages, logged-in users, cron, AJAX, REST API, bots, feeds.
- Weekly cron purges data older than 90 days.
- Table created on plugin activation; auto-created on version upgrade.

### NO RESIDENTES (`class-sgu-no-residentes.php`)

Manages the "No Residentes" (USA export) flag on vehicles:

- **ACF field**: `no_residentes` (true/false) registered on `post` and `defenders` post types.
- **Admin column**: Shows blue "NO RES." badge after title column. Always visible (no hover needed).
- **Quick Edit**: Checkbox in quick edit for bulk toggling.
- **AJAX toggle**: `sgu_toggle_no_residentes` endpoint for dashboard toggle buttons.
- **Frontend CSS**: Blue gradient badge on vehicle cards and single pages.

Vehicles with NO RES active get a `.sgu-row-nores` class in the dashboard (blue left border + tinted background).

### Admin Organizer (`class-sgu-admin-organizer.php`)

Reorganizes the WordPress admin sidebar into collapsible sections:

| Section | Color | Items |
|---------|-------|-------|
| Vehiculos | Blue | Posts, Defenders |
| Contenido | Green | Pages, Media, Comments |
| Marketing | Orange | Gravity Forms, Yoast, Subscriber, Instagram, Trustindex |
| Configuracion | Purple | Theme Options, ACF, Appearance, Settings |
| Administracion | Red | Plugins, Users, Security, File Manager, Migration, Tools, SG Upgrades |

Collapse state is saved in `localStorage`. The active section auto-expands. Dark theme (#111111) applied to sidebar and admin bar. Custom logo in admin bar.

## Apache / .htaccess Rules

Written on plugin activation, removed on deactivation. Marker: `SGU WebP Delivery`.

```apache
# WebP delivery (transparent to browser)
RewriteEngine On
RewriteCond %{HTTP_ACCEPT} image/webp
RewriteCond %{REQUEST_FILENAME} \.(jpe?g|png)$
RewriteCond %{REQUEST_FILENAME}.webp -f
RewriteRule (.+)\.(jpe?g|png)$ $1.$2.webp [T=image/webp,L]

# Vary header for CDN/proxy compatibility
Header append Vary Accept

# Browser caching
ExpiresByType image/*     "access plus 1 year"
ExpiresByType text/css    "access plus 1 month"
ExpiresByType application/javascript "access plus 1 month"
ExpiresByType font/*      "access plus 1 year"

# Gzip compression
AddOutputFilterByType DEFLATE text/html text/css application/javascript
                              application/json image/svg+xml font/woff font/woff2
```

**How WebP delivery works:**
1. Browser requests `photo.jpg`
2. Apache checks if browser accepts `image/webp` (via `Accept` header)
3. Apache checks if `photo.jpg.webp` exists on disk
4. If both true, serves the WebP file with `Content-Type: image/webp`
5. If either false, serves the original JPG/PNG as normal

No HTML changes needed. Completely transparent.

## Database

### Custom table: `wp_sgu_pageviews`

| Column | Type | Index |
|--------|------|-------|
| `id` | BIGINT AUTO_INCREMENT | PRIMARY |
| `url` | VARCHAR(500) | KEY (191 chars) |
| `referrer` | VARCHAR(500) | - |
| `created_at` | DATETIME | KEY |

### Post meta keys

| Key | Type | Description |
|-----|------|-------------|
| `_sgu_optimized` | `1` | Image has been optimized |
| `_sgu_optimization_data` | array | Optimization stats (sizes, dimensions, savings) |

### Options

| Key | Description |
|-----|-------------|
| `sgu_image_optimizer_settings` | Optimizer config (quality, max width, etc.) |
| `sgu_optimizer_global_stats` | Aggregate stats (total optimized, savings, WebP count) |
| `sgu_db_version` | Plugin version for DB migration tracking |
| `sgu_dismissed_entries` | Array of dismissed GF entry IDs (max 200) |
| `sgu_spam_entries` | Array of spam GF entry IDs (max 500) |
| `sgu_replied_entries` | Array of replied GF entry IDs (max 500) |

## ACF Fields

| Field Key | Name | Type | Post Types |
|-----------|------|------|------------|
| `field_sgu_no_residentes` | `no_residentes` | True/False | post, defenders |

Other ACF fields used (registered elsewhere, likely in theme or ACF plugin):
- `informations` (group with `year` subfield)
- `price`
- `sold` (value `v1` = sold)

## Common Tasks

### Adding a new vehicle
Just create a new Post or Defender in WordPress. Images are automatically optimized and WebP versions created on upload.

### Marking a vehicle as NO RESIDENTES
Three ways:
1. Toggle the ACF field in the post editor sidebar
2. Click the flag button in the dashboard vehicle list
3. Use Quick Edit in the Posts/Defenders list

### Regenerating WebP files
Go to **SG Upgrades > Image Optimizer** in admin. Use the "Regenerate WebP" button to batch-process all images with correct naming.

### Checking performance
Open the site in Chrome DevTools > Network tab. Look for:
- WebP files being served (check Content-Type header)
- Lazy loading attributes on images
- CSS files loaded via preload
- Cache headers on static assets

### Deploying changes
1. Make changes locally in this repo
2. Test if possible
3. Run `python3 deploy.py plugin` or `python3 deploy.py theme`
4. Verify on production

### Viewing logs
Go to **SG Upgrades > Logs** in the WordPress admin to see optimization and processing logs.
