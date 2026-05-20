# Small MVC Auth

![PHP Version Requirement](https://img.shields.io/badge/PHP-%3E%3D%208.0-777BB4?logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)
![Composer](https://img.shields.io/badge/Composer-Ready-blue?logo=composer&logoColor=white)

A lightweight, lightning-fast, and fully "headless" authentication plugin tailored for custom-built PHP MVC frameworks. 

The package handles all underlying logic for secure login, database validation, and sessions, but leaves all design, HTML, and CSS (UI) entirely up to you and your application. The perfect drop-in package when you want rock-solid authentication without being forced into a specific frontend library.

## ✨ Features
* **100% Headless:** No baked-in HTML. Works seamlessly with Bootstrap, Tailwind, Vue, React, or plain HTML.
* **Secure "Remember Me":** Built-in support for auto-login via secure, token-based HttpOnly cookies (protects against XSS and Cookie Forgery).
* **Dependency Injection:** Completely independent of your framework's underlying structure. You inject your own database connection.
* **Intended URL ("Smart Redirects"):** Automatically saves the URL the user attempted to access before being redirected to the login, and routes them to the correct destination upon successful login.
* **Rock-Solid Encryption:** Built on PHP's native and industry-standard `password_hash()` and `password_verify()`.

---

## 📦 Installation

Install the package via Composer in your project:

```bash
composer require raktfranhjartat/small-mvc-auth

```
## 🚀 Getting Started
### 1. Prepare the Database
The package expects you to have a table (e.g., users) with at least these three columns:
 * email (VARCHAR, UNIQUE)
 * password_hash (VARCHAR)
 * remember_token (VARCHAR, NULL) - *Used for the secure cookie.*
> **Tip:** To create your first test password, you can run echo password_hash('your_password', PASSWORD_DEFAULT); in a temporary PHP file and insert the result directly into the database.
> 
### 2. Initialize the Package
Import AuthManager into your application and pass your existing database connection (PDO or your custom database wrapper).
```php
use Raktfranhjartat\SmallMvcAuth\AuthManager;
use App\Core\Database; // Replace with your framework's database class

// Start your database (fetches config for 'app')
$db = new Database('app');

// Initialize the authentication package
$auth = new AuthManager($db);

```
### 3. Protect a Route (Middleware)
To lock a controller or method so that only logged-in users have access, call requireLogin().
If the user is not logged in, their requested URL is saved in the session, and they are redirected directly to /login.
```php
// Inside your Controller, before loading the view
$auth->requireLogin('/login');

// The code below only runs if the user is confirmed as logged in

```
### 4. Handle the Login
When your application receives a login form via POST, use attempt() to verify the credentials against the database.
If the login is successful, you can use the smart intendedUrl() function to send the user back exactly where they were headed.
```php
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']); // true if the checkbox is checked

if ($auth->attempt($email, $password, $remember)) {
    // Retrieves the URL they tried to reach, otherwise redirects to /admin
    $redirectUrl = $auth->intendedUrl('/admin'); 

    // Use the framework's built-in redirect method
    $this->redirect($redirectUrl, ['success' => 'You are logged in!']);
    return;
} else {
    // Incorrect email or password, redirect back to the form
    $this->redirect('/login', ['error' => 'Incorrect email or password.']);
    return;
}

```
## 🤝 Contributing
Pull requests are very welcome! If you find a bug or have suggestions for new features, please open an "Issue" first so we can discuss it. Make sure to follow the code standards before pushing your PR.
## 📄 License
This project is open-source and released under the MIT License. You are free to use, modify, and distribute the code, even in commercial projects.
```
