# SIMPEG Vulnerable Target

Aplikasi Web SIMPEG simulasi dengan kerentanan OWASP Top 10 untuk modul pelatihan Penetration Testing.

## Kerentanan Terdaftar
1. **SQL Injection (Login Bypass):** `src/login.php`
2. **Insecure Direct Object Reference (IDOR):** `src/download.php`
3. **Stored XSS:** `src/profile.php`
4. **Unrestricted File Upload (RCE):** `src/profile.php`

## Quick Start
```bash
docker-compose up -d --build
