# App Expert Password Reset

## Description
App Expert Password Reset is a WordPress plugin that provides a secure OTP-based password reset system through custom REST API endpoints. This plugin allows users to reset their passwords using a time-limited One-Time Password (OTP) sent to their email address, providing an additional layer of security beyond traditional password reset methods.

## Features
- Secure OTP generation and verification
- Time-limited OTP codes (10 minutes expiration)
- Three-step password reset process (send OTP, verify OTP, reset password)
- Custom REST API endpoints for integration with mobile apps or front-end applications
- Input validation and sanitization for security
- Error handling with appropriate HTTP status codes

## Installation
1. Download the plugin zip file.
2. Log in to your WordPress admin panel.
3. Navigate to **Plugins > Add New**.
4. Click **Upload Plugin** and select the downloaded zip file.
5. Click **Install Now**.
6. Activate the plugin.

## API Endpoints
The plugin provides three REST API endpoints:

### 1. Send OTP
- **Endpoint**: `/wp-json/app-expert/v1/send-otp`
- **Method**: `POST`
- **Parameters**:
    - `email` (required): User's registered email address
- **Response**:
    - **Success**: `{"message": "OTP sent successfully."}`
    - **Error**: `{"code": "email_not_found", "message": "Email not found.", "data": {"status": 404}}`

### 2. Verify OTP
- **Endpoint**: `/wp-json/app-expert/v1/verify-otp`
- **Method**: `POST`
- **Parameters**:
    - `email` (required): User's registered email address
    - `otp` (required): The OTP code received via email
- **Response**:
    - **Success**: `{"message": "OTP verified successfully."}`
    - **Error**:
        - `{"code": "email_not_found", "message": "Email not found.", "data": {"status": 404}}`
        - `{"code": "otp_expired", "message": "OTP has expired.", "data": {"status": 400}}`
        - `{"code": "otp_invalid", "message": "Invalid OTP.", "data": {"status": 400}}`

### 3. Reset Password
- **Endpoint**: `/wp-json/app-expert/v1/reset-password`
- **Method**: `POST`
- **Parameters**:
    - `email` (required): User's registered email address
    - `password` (required): New password
    - `confirm_password` (required): Confirmation of new password
- **Response**:
    - **Success**: `{"message": "Password reset successfully."}`
    - **Error**:
        - `{"code": "email_not_found", "message": "Email not found.", "data": {"status": 404}}`
        - `{"code": "password_mismatch", "message": "Passwords do not match.", "data": {"status": 400}}`
        - `{"code": "otp_not_verified", "message": "OTP not verified.", "data": {"status": 400}}`


## Security Considerations
- OTP codes expire after 10 minutes.
- All user inputs are sanitized.
- Password reset requires verification of OTP.
- User metadata related to OTP is cleaned after a successful password reset.

## Requirements
- WordPress 5.0 or higher
- PHP 7.0 or higher
