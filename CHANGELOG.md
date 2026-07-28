# Changelog

All notable changes to this project will be documented in this file.

## [v2.0.8] - 2026-07-28 - **SECURITY & STABILITY RELEASE**

### 🔐 **SECURITY FIXES**
- 🚨 **Critical**: Fixed a command-injection vulnerability in the dependency installer — `DependencyController::installPackage()` now validates the package name against a strict allow-list pattern and shells out via an argument-array `Process` instead of an interpolated shell string
- 🚨 **Critical**: Registered the 5 v2.0 middleware (`SecurityMiddleware`, `PerformanceMiddleware`, `ProgressMiddleware`, `DependencyMiddleware`, `ExceptionHandlerMiddleware`) on the `install`/`update` route groups — they were fully implemented but never wired into the service provider, so the advertised rate limiting, security headers, suspicious-content filtering, and audit logging were **not actually active** in any v2.0.x release before this one
- 🚨 **Critical**: `PurchaseController::validatePurchase()` no longer unconditionally bypasses license validation — it returned before running any of its own logic, so a purchase code was never actually checked. Now gated by `installer.purchase_validation.enabled` (defaults to `false`, preserving prior default behavior for consumers who don't sell through Envato; real validation runs when enabled)
- **High**: Added an optional IP allow-list / shared access-token gate for the entire install/update flow (`installer.security.allowed_ips`, `installer.security.access_token`) — previously the only gate was a `storage/installed` lock file that doesn't exist until installation is complete, leaving every route (including database migrate/rollback and dependency-install) reachable by anyone who got there first
- **High**: Sanitized `.env` writes in `CacheQueueManager::updateEnvFile()` to strip control characters, closing an env-line-injection path for the Redis queue-setup form (`EnvironmentManager` already had this protection; `CacheQueueManager` didn't)
- **High**: `DatabaseBackupManager::restoreBackup()` now validates the backup identifier format before building a filesystem path from it
- **Medium**: `mysqldump`/`mysql`/`pg_dump`/`psql` now receive database credentials via `MYSQL_PWD`/`PGPASSWORD` environment variables instead of a `--password=` CLI flag, so they're no longer visible in `ps aux` for the life of the process
- **Medium**: Backup directories/files are now created with `0700`/`0600` permissions instead of world-readable defaults
- **Medium**: Implemented the previously-declared-but-unused `audit.sensitive_fields` redaction in `LogManager` — passwords/secrets are now stripped before any operation is written to the audit log
- **Medium**: Fixed `EnvironmentController::checkDatabaseConnection()` mutating the application's ambient `config('database')` state during a connection test, which could leak the installer's test credentials into the rest of the request lifecycle

### 🐛 **BUG FIXES**
- Fixed `layouts/master.blade.php` referencing nonexistent route names (`LaravelInstaller::environment`/`LaravelInstaller::requirements`), which threw a `RouteNotFoundException` whenever an error page (or the resume-installation page) tried to render
- Fixed missing `createPostgresBackup`/`restorePostgresBackup` implementations — the driver switch in `DatabaseBackupManager` referenced them, but they didn't exist, so any PostgreSQL install would fatally error on backup
- Fixed `DatabaseOptimizer::optimizeMemoryUsage()` — memory-limit recommendations were based on the current process's own memory usage rather than the configured `memory_limit`, and a MySQL-only PDO attribute was being set unconditionally on all drivers (would warn/fail on PostgreSQL/SQLite, both advertised as supported)
- Fixed a broken `Dockerfile`/`docker-compose.yml`/`package.json` chain: `docker-compose.yml` mounted a nonexistent `docker/nginx/default.conf`, `Dockerfile` ran `npm run build` against a `package.json` with no `build` script, and the asset paths in `package.json` pointed at the wrong directory — `docker-compose up` and a from-scratch `npm run build` both failed before this release

### 🧹 **CLEANUP**
- Removed dead ad-hoc scripts `test-installer.php` / `test-package.php` from the repo root (not wired into CI, and `test-package.php` referenced a controller class that no longer exists)
- Removed ~190KB of orphaned pre-Tailwind `sass/`/`scss/` source trees, an unreferenced `style-prev.css`, and unused CSS source maps — none of it was reachable from the actual Tailwind build script or any view

### 📚 **DOCUMENTATION**
- Added `CODE_OF_CONDUCT.md` (referenced by `CONTRIBUTING.md` since it was written, but never existed)
- Rewrote `SECURITY.md`, which was an unmodified template: a fake supported-versions table (5.x/4.x for a package that has never shipped past 2.0.x) and a `security@example.com` placeholder contact
- Fixed the README license link (pointed at `LICENSE.md`; the file is `LICENSE`)
- Corrected the README's minimum PHP requirement (8.0 → 8.1, matching `composer.json`) and softened the "18+ languages supported" claim to reflect that most locales are partial

### ⚠️ **Known issues carried into this release**
- 14 of 19 non-English locale files remain incomplete (English is complete); see `CONTRIBUTING.md` if you'd like to help translate
- Long-running install operations (database migration, backup, dependency install) still run synchronously within the request instead of being queued — acceptable for typical installer usage, but can be slow against large databases

## [v2.0.7] - 2024-12-19 - **HOTFIX RELEASE**

### 🐛 **BUG FIXES**
- ✅ Fixed version compatibility logic for OR constraints (e.g., `^9.0|^10.0|^11.0`)
- ✅ Fixed PHP package handling in dependency checker
- ✅ Fixed dev version compatibility (e.g., `2.x-dev` now matches `^2.10.1`)
- ✅ Fixed dependencies auto-check on page load
- ✅ Added semi-transparent background to sidebar (`#ffffff66`)

### 🔧 **IMPROVEMENTS**
- ✅ Enhanced dependency checker with proper constraint parsing
- ✅ Improved error handling for dependency validation
- ✅ Better visual feedback for dependency status
- ✅ Auto-execution of dependency checks on page load
- ✅ Detailed dependency information display (required vs installed versions)

### 📱 **UI ENHANCEMENTS**
- ✅ Added background overlay to installation sidebar
- ✅ Improved dependency status indicators with icons
- ✅ Better error messages for failed dependency checks
- ✅ Enhanced visual hierarchy in dependency display

## [v2.0.0] - 2024-12-19 - **PRODUCTION RELEASE**

### 🚀 **MAJOR RELEASE - COMPLETE REWRITE**

### ✅ **NEW FEATURES IMPLEMENTED**

#### **Enhanced Security System**
- ✅ Global exception handlers with detailed logging (`InstallerExceptionHandler`)
- ✅ Input sanitization and XSS prevention (`SecurityMiddleware`)
- ✅ Rate limiting for installation endpoints (20 requests/5 minutes)
- ✅ Security middleware with suspicious content detection
- ✅ Comprehensive audit logging for compliance (`LogManager`)
- ✅ Security headers (CSP, XSS Protection, Frame Options)

#### **Database Backup & Recovery**
- ✅ Pre-migration database backup functionality (`DatabaseBackupManager`)
- ✅ Automatic rollback on failed installations (`DatabaseController::rollback`)
- ✅ Support for MySQL, PostgreSQL, and SQLite
- ✅ Backup cleanup and management with cache storage
- ✅ Batch migration support for large databases

#### **Progress Tracking & Resumability**
- ✅ Visual progress bars with step indicators (`ProgressTracker`)
- ✅ Installation state persistence with session storage
- ✅ Resumable installations after interruption (`ProgressMiddleware`)
- ✅ Real-time progress updates via AJAX API
- ✅ 9-step installation flow with validation

#### **Performance Monitoring**
- ✅ Execution time tracking with microsecond precision (`PerformanceMonitor`)
- ✅ Memory usage monitoring and optimization
- ✅ Performance dashboard with real-time metrics and charts
- ✅ Database optimization for large installations (`DatabaseOptimizer`)
- ✅ Performance headers in HTTP responses

#### **Dependency Management**
- ✅ Composer dependency validation (`DependencyChecker`)
- ✅ Version compatibility checking with detailed reports
- ✅ Critical dependency enforcement (`DependencyMiddleware`)
- ✅ Package installation automation via shell commands
- ✅ Interactive dependency resolution interface

#### **Cache & Queue Setup**
- ✅ Automated cache clearing and optimization (`CacheQueueManager`)
- ✅ Queue driver configuration (sync, database, Redis)
- ✅ Task scheduler setup with cron generation
- ✅ Application performance optimization commands
- ✅ Redis configuration with authentication support

### 🔧 **ENHANCED FEATURES**

#### **Complete 9-Step Installation Process**
1. ✅ Welcome Screen
2. ✅ Server Requirements Check
3. ✅ File Permissions Validation
4. ✅ **Dependencies Check** (NEW)
5. ✅ Environment Configuration
6. ✅ Database Configuration
7. ✅ **Database Backup & Migration** (NEW)
8. ✅ **Cache & Queue Setup** (NEW)
9. ✅ **Performance Dashboard** (NEW)

#### **New Controllers & APIs**
- ✅ `DependencyController` - Composer dependency management
- ✅ `PerformanceController` - Real-time performance monitoring
- ✅ `ProgressController` - Installation progress tracking
- ✅ `CacheQueueController` - Cache and queue configuration
- ✅ Enhanced `DatabaseController` - Backup and rollback support

#### **New Middleware System**
- ✅ `SecurityMiddleware` - XSS protection and rate limiting
- ✅ `PerformanceMiddleware` - Execution time and memory tracking
- ✅ `ProgressMiddleware` - Installation step validation
- ✅ `DependencyMiddleware` - Critical dependency checking
- ✅ `ExceptionHandlerMiddleware` - Global error handling

#### **New Views & UI**
- ✅ `dependencies.blade.php` - Interactive dependency checking
- ✅ `performance-dashboard.blade.php` - Real-time metrics dashboard
- ✅ `cache-queue.blade.php` - Cache and queue setup interface
- ✅ `database-backup.blade.php` - Backup and migration interface
- ✅ `resume-installation.blade.php` - Installation resumability

### 🛡️ **SECURITY ENHANCEMENTS**
- ✅ Input validation and sanitization for all forms
- ✅ Rate limiting and abuse prevention (IP-based)
- ✅ Security headers and CSP implementation
- ✅ Audit logging for compliance tracking
- ✅ XSS and injection attack prevention
- ✅ Suspicious content detection and blocking

### ⚡ **PERFORMANCE IMPROVEMENTS**
- ✅ Memory usage optimization with garbage collection
- ✅ Database query optimization for large datasets
- ✅ Cache management and optimization
- ✅ Performance metrics and real-time monitoring
- ✅ Batch processing for large operations
- ✅ OPCache integration and optimization

### 🌐 **MULTI-LANGUAGE SUPPORT**
- ✅ Extended translations for all new features
- ✅ Support for 18+ languages
- ✅ Contextual help text and descriptions

### 📦 **PACKAGE IMPROVEMENTS**
- ✅ Enhanced service provider with middleware registration
- ✅ Console commands for cache management
- ✅ Improved configuration publishing
- ✅ Better asset management and optimization

### 🔄 **API ENDPOINTS**
- ✅ `/api/progress` - Get installation progress
- ✅ `/api/dependencies/check` - Check dependencies
- ✅ `/api/performance/metrics` - Get performance metrics
- ✅ `/api/cache/clear` - Clear application caches
- ✅ `/api/database/migrate` - Run database migrations
- ✅ `/api/database/rollback` - Rollback database changes

## [v1.0.3] - Previous Release
- Basic installation functionality
- Environment configuration
- Database setup
- Purchase code validation

## [v1.0.2] - Previous Release
- Initial release features

## [v1.0.1] - Previous Release
- Bug fixes and improvements

## [v1.0.0] - Initial Release
- Basic Laravel installer functionality