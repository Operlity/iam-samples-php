# IdentityHub PHP Integration Sample

A clean, premium PHP web application demonstrating **OpenID Connect (OIDC)** authentication with **IdentityHub** and a local **Contact Management module** (CRUD).

---

## ⚙️ Features

- **🔐 Secure OIDC Authentication**: Implements OpenID Connect Authorization Code Flow with PKCE.
- **📇 Contact Management**: A local database module to Add, Edit, Delete, and Search contacts.
- **📁 SQLite Storage**: All contact data is persisted locally in an SQLite database file.
- **🎨 Glassmorphic UI**: A dark-mode interface with smooth transitions and responsive controls.

---

## 📦 Prerequisites

Ensure you have the following installed locally:
- **PHP 8.0+** (with `curl`, `openssl`, `mbstring`, and `pdo_sqlite` extensions enabled in your `php.ini`).
- **Composer** (PHP dependency manager).
- **Node.js** (optional, only required if running the local HTTPS proxy).

---

## 🚀 Setup & Installation

### 1. Install Dependencies
Run Composer to install the OIDC library:
```bash
composer install
```

### 2. Configure Settings
Copy the example configuration to create your local config:
```bash
cp config.php.example config.php
```
Open `config.php` and fill in your OIDC Client credentials:
- `client_id`
- `client_secret`
- `redirect_uri` (e.g., `https://localhost:7284/signin-oidc`)

---

## 🏃 Running the Application

OIDC requires a secure context (`HTTPS`) for callbacks. You can run the application locally in one of two ways:

### Option A: Running with local HTTPS Proxy (Recommended)
This uses the included self-signed certificates and a NodeJS proxy. Run the cross-platform starter script to boot both servers concurrently in one terminal:

1. **Run the starter script**:
   ```bash
   php start.php
   # OR if npm is preferred
   npm start
   ```
2. Open your browser and navigate to:
   `https://localhost:7284` (or `https://localhost:4500` if using the alternative port).

---

### Option B: Running with HTTP (If permitted by issuer)
If your OIDC provider permits local HTTP redirection:

1. Update `config.php` and set the `redirect_uri` to:
   `http://localhost:8000/signin-oidc`
2. Start the PHP server:
   ```bash
   php -S localhost:8000 router.php
   ```
3. Navigate to:
   `http://localhost:8000`

---

## 🛡️ License

This project is licensed under the MIT License - see the `LICENSE` file for details.
