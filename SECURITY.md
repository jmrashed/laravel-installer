# Security Policy

## Supported Versions

We actively support the following versions with security updates:

| Version | Supported          |
| ------- | ------------------ |
| 2.0.x   | :white_check_mark: |
| < 2.0   | :x:                |

## Reporting a Vulnerability

If you discover a security vulnerability in Laravel Installer, please report it privately rather than opening a public issue:

1. **Do not** create a public GitHub issue for the vulnerability.
2. Open a [private security advisory](https://github.com/jmrashed/laravel-installer/security/advisories/new) on GitHub, or email **jmrashed@gmail.com** with details.
3. Include:
   - A clear description of the vulnerability
   - Steps to reproduce the issue
   - Potential impact
   - Any suggested fixes (optional)

## What to Expect

- We will acknowledge receipt of your report within 5 business days.
- We will investigate the issue and provide regular updates until it's resolved.
- We will credit you (if desired) once the vulnerability is fixed.
- We will not disclose details of the vulnerability publicly until a fix is released.

## Security Best Practices for Users of This Package

- Keep this package and your Laravel framework version up to date.
- The install wizard has no authentication of its own beyond a lock file
  (`storage/installed`) written at the end of installation. On a
  publicly reachable deployment, set `INSTALLER_ACCESS_TOKEN` and/or
  `INSTALLER_ALLOWED_IPS` (see `config/installer.php` →
  `security.access_token` / `security.allowed_ips`) before the app is
  reachable, and remove/rotate them once installation is complete.
- Serve the installer over HTTPS only.
- Back up your application and database before running the installer or
  updater against a non-empty environment.
- Review `storage/logs/installer-audit.log` after installation for any
  unexpected activity.

## Responsible Disclosure

We kindly ask that you follow responsible disclosure practices:

- Give us reasonable time to fix the issue before public disclosure.
- Avoid accessing or modifying data beyond what's needed to demonstrate the issue.
- Do not perform denial-of-service testing or degrade service for others.
- Respect user privacy and applicable data protection laws.

Thank you for helping keep Laravel Installer and its users secure.
