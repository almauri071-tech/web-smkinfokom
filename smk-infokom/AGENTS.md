# Project Instructions

## Project Structure
- This is a server-rendered PHP school website backed by MySQL and PDO.
- Public pages are in the project root: `index.php`, `halaman.php`, `jurusan.php`, `gambar.php`, and `dokumen.php`.
- `admin.php` handles administration; shared PHP modules and page chrome live in `includes/`.
- `assets/style.css` contains the site's styles. `database/smk_infokom.sql` is the database schema and seed data.
- Uploaded site images and PPDB documents are stored under `uploads/`.

## Local Setup
- Run the project with Apache and MySQL from XAMPP.
- Import `database/smk_infokom.sql` into MySQL. Database connection settings are in `includes/db.php`.
- Open the site through the local Apache URL; PHP pages require a web server and should not be opened as static files.

## Development Conventions
- Keep changes focused and follow the existing procedural PHP style and Indonesian user-facing copy.
- Reuse shared helpers in `includes/` rather than duplicating database, content, image, or document handling.
- Use PDO prepared statements for values in SQL queries. Escape dynamic HTML with the existing `e()` helper.
- Preserve CSRF checks and existing validation in admin POST handlers. For uploaded files, follow the existing image/document helpers and upload limits; do not trust client-provided filenames or MIME types.
- Keep public-page content and presentation consistent with the existing root-page and `assets/style.css` patterns.
- Do not add dependencies or build tooling unless the task requires them; no package manager or automated test suite is currently configured.

## Validation
- Syntax-check changed PHP files with `php -l path/to/file.php`.
- For a broader PHP syntax check in PowerShell, run:
  `Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }`
- For changes involving database behavior, also verify the affected workflow against the imported MySQL schema through the local XAMPP site.