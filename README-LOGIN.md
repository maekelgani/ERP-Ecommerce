# Setup Guide - Dual Authentication System

## Ringkasan Sistem
Website Nano Komputer sekarang memiliki sistem login terpisah:
1. **Customer Login** (`login-customer.php`) - Untuk pelanggan
2. **Admin Login** (`login.php`) - Untuk administrator

## Akun Default Admin

### Super Admin
- Email: `fajarnanokomp@gmail.com`
- Password: `SuperAdmin22!`
- Role: `owner`

### Admin 1
- Email: `faizalardi@gmail.com`
- Password: `#backenDev22`
- Role: `admin`

### Admin 2
- Email: `maekelgani@gmail.com`
- Password: `#frontenDev11`
- Role: `admin`

### Admin 3
- Email: `isfahankaefal@gmail.com`
- Password: `fancyUhuy_`
- Role: `admin`

## Struktur File

\`\`\`
config/
├── DatabaseManager.php      # Singleton database connection
├── CustomerAuth.php         # Customer login/register logic
├── AdminAuth.php           # Admin login logic only
├── RBACManager.php         # Role-Based Access Control
├── PasswordHasher.php      # Password hashing utilities
└── PasswordHashGenerator.php # Generate hashes for seeding

view/
├── login-customer.php       # Customer login & register page
├── login.php               # Admin login page (NO register, NO Google)
├── logout.php              # Unified logout (redirects based on user type)
└── redirect.php            # Google OAuth callback (customer only)

scripts/
├── 02_create_customers_table.sql      # Customers table schema
├── 03_create_administrators_table.sql # Administrators table schema
└── 04_seed_administrators_v2.sql      # Admin accounts seed
\`\`\`

## Flow & Session

### Customer Flow
1. Customer buka `login-customer.php`
2. Login manual (email/phone + password) ATAU Google OAuth
3. Session variables:
   - `$_SESSION['customer_id']`
   - `$_SESSION['customer_name']`
   - `$_SESSION['customer_email']`
   - `$_SESSION['role']` = 'customer'
   - `$_SESSION['login_type']` = 'manual' atau 'google'

4. Redirect ke `/users/landingPage.php`

### Admin Flow
1. Admin buka `login.php`
2. Login manual dengan email + password (HANYA)
3. Session variables:
   - `$_SESSION['admin_id']`
   - `$_SESSION['admin_name']`
   - `$_SESSION['admin_email']`
   - `$_SESSION['admin_role']` = 'owner' atau 'admin'
   - `$_SESSION['role']` = admin role (untuk RBAC)
   - `$_SESSION['login_type']` = 'admin'

4. Redirect ke `/admin/DashboardAdmin.php`

### Logout
- Both `logout.php` handles logout untuk customer dan admin
- Redirect berdasarkan login type yang sebelumnya

## Fitur Keamanan

### Password
- Menggunakan Bcrypt (PHP `password_hash` & `password_verify`)
- Cost: 10
- Tidak menggunakan MD5

### Database
- Prepared Statements (prevent SQL Injection)
- Input validation dan sanitization

### Session
- Session-based authentication
- Check role untuk RBAC

### Google OAuth
- Hanya untuk customer
- Admin TIDAK bisa login via Google
- Auto-create customer account saat pertama kali Google login

## Customization

### Membuat Admin Account Baru (Admin Only)

Di masa depan, super admin bisa membuat admin account baru melalui dashboard admin.

### Menambah Role Baru

1. Edit `config/RBACManager.php` - tambah role constant
2. Update `$hierarchy` dalam method `hasPermission()`
3. Update route protection

### Mengubah Password Admin

Generate hash baru:
\`\`\`bash
php config/PasswordHashGenerator.php
\`\`\`

Kemudian update database secara manual dengan hash baru.

## Troubleshooting

### Error: "Database Connection Error"
- Check `.env` file untuk DB_HOST, DB_USER, DB_PASSWORD, DB_NAME
- Pastikan MySQL server running

### Error: "Email atau password salah" (Admin)
- Verify email tepat di database
- Generate hash baru jika password error
- Check admin status = 'active'

### Error: "Akun tidak aktif"
- Update status menjadi 'active' di database
\`\`\`sql
UPDATE administrators SET status = 'active' WHERE email = 'admin@example.com';
\`\`\`

### Google OAuth tidak bekerja (Customer)
- Check GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET di `.env`
- Check GOOGLE_REDIRECT_URI = `http://localhost/view/redirect.php` (development)
- Ensure Google OAuth is enabled di Google Cloud Console

## Next Steps

1. Setup customer table permissions (RLS jika pakai database dengan fitur tersebut)
2. Implementasi "Lupa Password" functionality
3. Implementasi admin management panel (create/edit/delete admin)
4. Add audit logging untuk admin actions
5. Implementasi two-factor authentication (2FA)

## Support

Untuk pertanyaan atau masalah, silakan hubungi development team.
