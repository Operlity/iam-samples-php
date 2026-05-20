# IdentityHub PHP Integration Sample

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![Node.js](https://img.shields.io/badge/Node.js-Required-green.svg)](https://nodejs.org)

A premium PHP web application demonstrating **OpenID Connect (OIDC)** authentication with **IdentityHub** using the **Authorization Code Flow**. This sample showcases secure authentication, session management, and modern UI design.

---

## 📋 Table of Contents
- [Features](#-features)
- [Prerequisites](#-prerequisites)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Running the Application](#-running-the-application)
- [Project Structure](#-project-structure)
- [Technical Architecture](#-technical-architecture)
- [Troubleshooting](#-troubleshooting)
- [Security Considerations](#-security-considerations)
- [License](#-license)

---

## ✨ Features

- **🔐 Secure OIDC Authentication**: Implements OpenID Connect Authorization Code Flow
- **🛡️ Session Management**: Server-side sessions with secure, HTTP-only cookies
- **🎨 Premium UI**: Modern dark-mode interface with glassmorphism and smooth animations
- **🔄 Auto-Logout**: Complete session destruction (local + IdentityHub)
- **🔒 HTTPS Support**: Local HTTPS proxy using self-signed certificates
- **📊 User Claims Display**: Shows all OIDC claims and user information
- **🚀 Easy Setup**: Automated startup script with comprehensive error handling

---

## 📦 Prerequisites

Before you begin, ensure you have the following installed:

### Required Software
- **PHP 8.0+** with extensions:
  - `curl` - For HTTP requests
  - `openssl` - For HTTPS/SSL support
  - `mbstring` - For string handling
- **Composer** - PHP dependency manager ([Download](https://getcomposer.org/))
- **Node.js** - For HTTPS proxy ([Download](https://nodejs.org/))

### IdentityHub Account
- Access to an IdentityHub instance (e.g., https://id.demo.operlity.com)
- Registered OAuth 2.0 client with:
  - Client ID
  - Client Secret
  - Redirect URI configured

---

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/Operlity/iam-samples-php.git
cd iam-samples-php
```

### 2. Install PHP (Windows)

**Option A: Manual Installation (Recommended for this project)**
1. Download PHP from [windows.php.net/download](https://windows.php.net/download/)
2. Choose **"VS16 x64 Thread Safe"** ZIP version
3. Extract to `D:\PHP Web App\tools\php\`
4. The script will auto-configure PHP

**Option B: Via Package Manager**
```powershell
# Chocolatey
choco install php

# Scoop
scoop install php
```
*Note: Update `$PHP_EXE` variable in `start.ps1` if using this method*

### 3. Install Composer Dependencies
```bash
composer install
```

### 4. Install Node.js Dependencies (if any)
The project uses Node.js for the HTTPS proxy. Node.js should already have the required `https`, `http`, and `fs` modules (built-in).

---

## ⚙️ Configuration

### 1. Configure IdentityHub Settings
Edit `config.php` and update with your IdentityHub credentials:

```php
return [
    'issuer' => 'https://id.demo.operlity.com',
    'client_id' => 'your-client-id-here',
    'client_secret' => 'your-client-secret-here',
    'redirect_uri' => 'https://localhost:4500/signin-oidc',
    'scopes' => ['openid', 'profile', 'email'],
    'end_session_endpoint' => 'https://id.demo.operlity.com/connect/endsession'
];
```

### 2. SSL Certificates
The project includes self-signed SSL certificates in the `certificates/` folder:
- `cert.pem` - SSL certificate
- `key.pem` - Private key

**For production**, replace these with valid certificates from a trusted CA.

---

## 🏃 Running the Application

### Quick Start (Windows)
```powershell
.\start.ps1
```

This script will:
1. ✅ Verify PHP installation and configuration
2. ✅ Start PHP development server on `http://localhost:8000`
3. ✅ Start Node.js HTTPS proxy on `https://localhost:4500`
4. ✅ Automatically open your browser to `https://localhost:4500`

### Manual Start

**Terminal 1 - PHP Server:**
```bash
D:\PHP Web App\tools\php\php.exe -S localhost:8000 router.php
```

**Terminal 2 - HTTPS Proxy:**
```bash
node proxy.js
```

**Browser:**
Navigate to `https://localhost:4500`

### Stopping the Application
Press `Enter` in the PowerShell terminal where `start.ps1` is running, or press `Ctrl+C` in both terminals if started manually.

---

## 📁 Project Structure

```
D:\PHP Web App\
├── certificates/           # SSL certificates for HTTPS
│   ├── cert.pem           # Self-signed SSL certificate
│   └── key.pem            # Private key
├── css/                   # Stylesheets
│   └── style.css         # Modern dark-mode UI styling
├── tools/                # Local tools (gitignored)
│   └── php/              # PHP installation
│       ├── php.exe       # PHP executable
│       ├── php.ini       # PHP configuration
│       ├── cacert.pem    # CA certificate bundle
│       └── ext/          # PHP extensions
├── vendor/               # Composer dependencies
├── config.php            # IdentityHub OIDC configuration
├── index.php             # Authentication handler & router
├── welcome.php           # Post-login user dashboard
├── logout.php            # Logout handler
├── router.php            # PHP built-in server router
├── proxy.js              # Node.js HTTPS proxy
├── start.ps1             # Windows PowerShell startup script
├── composer.json         # PHP dependencies
├── .gitignore           # Git ignore rules
├── CLEANUP.md           # Project cleanup instructions
└── README.md            # This file
```

### Key Files Explained

| File | Purpose |
|------|---------|
| `index.php` | Handles OIDC authentication flow, session management, and routing |
| `welcome.php` | Displays authenticated user info and OIDC claims |
| `logout.php` | Destroys session and redirects to IdentityHub logout |
| `config.php` | Central configuration for OIDC settings |
| `router.php` | Routes requests for PHP's built-in server |
| `proxy.js` | HTTPS-to-HTTP proxy (required for OIDC over HTTPS) |
| `start.ps1` | Automated startup script with error handling |

---

## 🔧 Technical Architecture

### Authentication Flow

```
┌─────────┐                 ┌──────────────┐                 ┌─────────────┐
│ Browser │                 │  PHP Server  │                 │ IdentityHub │
└────┬────┘                 └──────┬───────┘                 └──────┬──────┘
     │                             │                                │
     │  1. Access App              │                                │
     ├────────────────────────────>│                                │
     │                             │                                │
     │  2. Redirect to Login       │  3. Auth Request              │
     │<────────────────────────────┼───────────────────────────────>│
     │                             │                                │
     │  4. User Login              │                                │
     ├────────────────────────────────────────────────────────────>│
     │                             │                                │
     │  5. Auth Code               │                                │
     │<────────────────────────────┼────────────────────────────────┤
     │                             │                                │
     │  6. Send Auth Code          │  7. Exchange Code for Tokens  │
     ├────────────────────────────>┼───────────────────────────────>│
     │                             │                                │
     │                             │  8. Return Tokens & Claims    │
     │                             │<───────────────────────────────┤
     │                             │                                │
     │  9. Show Welcome Page       │                                │
     │<────────────────────────────┤                                │
     │                             │                                │
```

### Technology Stack

- **Backend**: PHP 8.0+ with OpenID Connect Client library
- **Frontend**: HTML5, CSS3 (Glassmorphism design)
- **Authentication**: OpenID Connect (Authorization Code Flow)
- **Session Storage**: PHP Sessions (server-side)
- **HTTPS Proxy**: Node.js with native `https` module
- **Dependencies**: 
  - `jumbojett/openid-connect-php` - OIDC client
  - `phpseclib/phpseclib` - Cryptographic operations

### Security Features

- ✅ HTTPS-only communication
- ✅ Secure session cookies (HTTPOnly, Secure, SameSite=Lax)
- ✅ CSRF protection via OIDC state parameter
- ✅ SSL certificate verification
- ✅ Server-side session management
- ✅ No client-side token storage

---

## 🐛 Troubleshooting

### Common Issues

#### 1. "PHP executable not found"
**Solution**: Install PHP using the instructions in the [Installation](#-installation) section.

#### 2. "OpenIDConnect needs the CURL PHP extension"
**Solution**: The `start.ps1` script automatically configures CURL. If you still see this error:
```powershell
# Verify CURL is enabled
D:\PHP Web App\tools\php\php.exe -m | Select-String "curl"
```

#### 3. "SSL certificate OpenSSL verify result: unable to get local issuer certificate"
**Solution**: The script automatically downloads and configures CA certificates. Verify:
```powershell
# Check if cacert.pem exists
Test-Path "D:\PHP Web App\tools\php\cacert.pem"
```

#### 4. Browser shows "Your connection is not private"
**Solution**: This is expected with self-signed certificates. Click "Advanced" → "Proceed to localhost (unsafe)" for development.

#### 5. "Cannot remove .vs folder"
**Solution**: Close Visual Studio completely, then run:
```powershell
Remove-Item "D:\PHP Web App\.vs" -Recurse -Force
```

### Debugging Tips

**Enable PHP Error Display** (development only):
```php
// Add to top of index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

**Check PHP Configuration**:
```bash
php -i | grep -E "curl|openssl|mbstring"
```

**View PHP Errors**:
Check the PHP built-in server output in the terminal.

---

## 🔒 Security Considerations

### Development vs. Production

#### Development (Current Setup)
- ✅ Self-signed SSL certificates
- ✅ Hardcoded client secrets in `config.php`
- ✅ PHP built-in server
- ⚠️ Error display enabled

#### Production Recommendations
- 🔴 Use valid SSL certificates from a trusted CA (Let's Encrypt, etc.)
- 🔴 Store secrets in environment variables or secure vaults
- 🔴 Use production-grade web server (Apache, Nginx)
- 🔴 Disable error display (`display_errors = Off`)
- 🔴 Enable PHP OPcache
- 🔴 Implement rate limiting
- 🔴 Add HTTPS security headers (HSTS, CSP, etc.)
- 🔴 Regular security updates

### Best Practices Implemented
- ✅ Authorization Code Flow (most secure OIDC flow)
- ✅ State parameter for CSRF protection
- ✅ HTTPOnly cookies prevent XSS attacks
- ✅ SameSite cookie attribute prevents CSRF
- ✅ Secure flag ensures HTTPS-only cookies
- ✅ Server-side session management
- ✅ No tokens stored in localStorage/sessionStorage

---

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🤝 Contributing

Contributions, issues, and feature requests are welcome! Feel free to check the [issues page](https://github.com/Operlity/iam-samples-php/issues).

---

## 📞 Support

For questions or support:
- **IdentityHub Documentation**: [https://docs.operlity.com](https://docs.operlity.com)
- **GitHub Issues**: [Report an issue](https://github.com/Operlity/iam-samples-php/issues)

---

## 🙏 Acknowledgments

- **OpenID Connect**: [openid.net](https://openid.net/)
- **jumbojett/openid-connect-php**: OIDC client library
- **Operlity IdentityHub**: Identity and Access Management platform

---

**Built with ❤️ for secure authentication**
