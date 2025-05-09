# Secure OTP-Based Password Reset System for WordPress


A robust password reset solution implementing military-grade security practices through custom REST API endpoints and time-limited OTP verification.

---

## Technical Overview

### System Flow
```mermaid
sequenceDiagram
    participant User
    participant ClientApp
    participant WordPress
    User->>ClientApp: Initiate Password Reset
    ClientApp->>WordPress: POST /send-otp (email)
    WordPress-->>ClientApp: OTP Sent Confirmation
    ClientApp-->>User: Check Email for OTP
    User->>ClientApp: Submit OTP + New Password
    ClientApp->>WordPress: POST /verify-otp (email, otp)
    WordPress-->>ClientApp: OTP Verification Status
    ClientApp->>WordPress: POST /reset-password (email, pwd, confirm_pwd)
    WordPress-->>ClientApp: Password Reset Confirmation
```

### Core Components
```
app-expert-password-reset/
├── app-expert-password-reset.php             # Main plugin file (entry point)
├── includes/                                 # Contains core functionality files
│   ├── class-reset-password-api.php          # Handles API endpoints (send OTP, verify OTP, reset password)
│   ├── indexr.php                            # Bootstrap or helper file (if needed)
│   └── general/                              # General utility functions
│       └── class-utility.php                 # Utility/helper functions for the plugin
└── config.php                                # Configuration file (e.g., constants, settings)                                
```

---

## Technical Specifications

### API Endpoint Matrix

| Endpoint               | Method | Parameters                      | Success Response          | Error Codes               |
|------------------------|--------|---------------------------------|---------------------------|--------------------------|
| `/send-otp`            | POST   | `email` (string)               | 200: OTP sent             | 404: Email not found     |
| `/verify-otp`          | POST   | `email` (string), `otp` (int)  | 200: OTP verified         | 400: Invalid/Expired OTP |
| `/reset-password`       | POST   | `email` (string), `password` (string), `confirm_password` (string) | 200: Password updated | 400: Validation failed |

---

## Security Implementation

```mermaid
graph TD
    A[User Request] --> B{Input Sanitization}
    B --> C[Email Validation]
    C --> D[OTP Generation]
    D --> E[Secure Metadata Storage]
    E --> F[Time-Based Expiration]
    F --> G[Password Hashing]
    G --> H[Metadata Cleanup]
```

---

## Installation & Configuration

### Requirements
- WordPress ≥5.8 (REST API support)
- PHP ≥7.4 
- SMTP server configured for email delivery

### Installation
1. Download the plugin files.
2. Upload the plugin folder to the `wp-content/plugins/` directory.
3. Activate the plugin through the **Plugins** menu in WordPress.


### Configuration
1. Set environment variables in `config.php`:
```php
define('OTP_EXPIRATION', 600);  // 10 minutes in seconds
define('OTP_LENGTH', 6);        // 6-digit code
define('MAX_ATTEMPTS', 3);      // Maximum verification attempts
```

---

## Database Schema

### User Meta Structure
| Meta Key                    | Data Type | Description                                 |
|-----------------------------|-----------|---------------------------------------------|
| `appExpert_otp`             | INT(6)    | Generated OTP code                          |
| `appExpert_otp_expires`     | TIMESTAMP | OTP expiration time                         |
| `appExpert_otp_verified`    | BOOLEAN   | OTP verification status                     |
| `appExpert_otp_attempts`    | INT       | Number of failed verification attempts      |
| `appExpert_otp_last_attempt`| TIMESTAMP | Timestamp of last OTP verification attempt  |

**Meta Cleanup Process**:
```php
delete_user_meta($user_id, 'appExpert_otp');
delete_user_meta($user_id, 'appExpert_otp_expires');
delete_user_meta($user_id, 'appExpert_otp_verified');
delete_user_meta($user_id, 'appExpert_otp_attempts');
delete_user_meta($user_id, 'appExpert_otp_last_attempt');

```

---

## Professional Email Template Design

**Location**: `email-templates/password-reset-email.php`

**Template Includes**:
- Mobile-responsive structure
- Branded header/footer sections
- Clear visual hierarchy for OTP display
- Dynamic variables for personalization:
  ```php
  <?= htmlspecialchars($user_name) ?>
  <?= htmlspecialchars(APP_NAME) ?>
  <?= htmlspecialchars((OTP_EXPIRATION / 60)) ?>
  ```

**Preview**:  
![Screenshot from 2025-05-08 16-00-30](https://github.com/user-attachments/assets/58e9327b-a712-4d00-9b19-d0524db52b89)

---

## Error Handling Matrix

| Error Code | Scenario                  | Resolution Path                     |
|------------|---------------------------|-------------------------------------|
| 400        | Invalid OTP               | Retry verification flow            |
| 401        | Unauthorized access       | Validate API credentials           |
| 404        | User not found            | Verify email registration          |
| 429        | Rate limit exceeded       | Implement exponential backoff      |
| 500        | Internal server error     | Check server logs & diagnostics    |

