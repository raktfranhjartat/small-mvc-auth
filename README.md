# Small MVC Auth 🔒

![PHP Version Requirement](https://img.shields.io/badge/PHP-%3E%3D%208.0-777BB4?logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)
![Composer](https://img.shields.io/badge/Composer-Ready-blue?logo=composer&logoColor=white)

Ett lättviktigt, blixtsnabbt och helt "headless" inloggningsplugin, skräddarsytt för egenbyggda PHP MVC-ramverk. 

Paketet hanterar all bakomliggande logik för säker inloggning, databasvalidering och sessioner, men lämnar all design, HTML och CSS (UI) helt upp till dig och din applikation. Det perfekta drop-in-paketet när du vill ha stensäker inloggning utan att tvingas in i ett specifikt frontend-bibliotek.

## ✨ Funktioner
* **100 % Headless:** Ingen inbakad HTML. Fungerar sömlöst med Bootstrap, Tailwind, Vue, React eller ren HTML.
* **Säker "Kom ihåg mig":** Inbyggt stöd för auto-inloggning via säkra, token-baserade HttpOnly-cookies (skyddar mot XSS och Cookie Forgery).
* **Dependency Injection:** Helt oberoende av ditt ramverks underliggande struktur. Du skickar in din egen databasanslutning.
* **Intended URL ("Smarta Redirects"):** Sparar automatiskt den URL användaren försökte nå innan de skickades till inloggningen, och slussar dem rätt när inloggningen lyckas.
* **Stensäker Kryptering:** Bygger på PHP:s inbyggda och branschstandardiserade `password_hash()` och `password_verify()`.

---

## 📦 Installation

Installera paketet via Composer i ditt projekt:

```bash
composer require raktfranhjartat/small-mvc-auth
```

---

## 🚀 Kom igång

### 1. Förbered Databasen
Paketet förväntar sig att du har en tabell (exempelvis `users`) med åtminstone dessa tre kolumner:
* `email` (VARCHAR, UNIQUE)
* `password_hash` (VARCHAR)
* `remember_token` (VARCHAR, NULL) - *Används för den säkra cookien.*

> **Tips:** För att skapa ditt första test-lösenord kan du köra `echo password_hash('ditt_lösenord', PASSWORD_DEFAULT);` i en tillfällig PHP-fil och lägga in resultatet direkt i databasen.

### 2. Initiera Paketet
Importera `AuthManager` i din applikation och skicka med din befintliga databasanslutning (PDO eller din egen databas-wrapper).

```php
use Raktfranhjartat\SmallMvcAuth\AuthManager;
use App\Core\Database; // Byt ut mot ditt ramverks databasklass

// Starta din databas (hämtar config för 'app')
$db = new Database('app');

// Koppla in inloggningspaketet
$auth = new AuthManager($db);
```

### 3. Skydda en Route (Middleware)
För att låsa en controller eller metod så att endast inloggade användare har åtkomst, anropar du `requireLogin()`. 

Om användaren inte är inloggad sparas deras sökta URL i sessionen, och de omdirigeras direkt till `/login`.

```php
// Inuti din Controller, innan du laddar vyn
$auth->requireLogin('/login');

// Koden här under körs bara om användaren är bekräftat inloggad
```

### 4. Hantera Inloggningen
När din applikation tar emot ett inloggningsformulär via POST använder du `attempt()` för att verifiera uppgifterna mot databasen. 

Om inloggningen lyckas kan du använda den smarta funktionen `intendedUrl()` för att skicka tillbaka användaren exakt dit de var på väg.

```php
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']); // true om checkboxen är ikryssad

if ($auth->attempt($email, $password, $remember)) {
    // Hämtar URL:en de försökte nå, annars skickas de till /admin
    $redirectUrl = $auth->intendedUrl('/admin'); 
    
    // Använd ramverkets inbyggda redirect-metod
    $this->redirect($redirectUrl, ['success' => 'Du är inloggad!']);
    return;
} else {
    // Fel e-post eller lösenord, skicka tillbaka till formuläret
    $this->redirect('/login', ['error' => 'Fel e-postadress eller lösenord.']);
    return;
}
```

---

## 🤝 Bidra (Contributing)
Pull requests är mycket välkomna! Om du hittar en bugg eller har förslag på nya funktioner, öppna gärna ett "Issue" först så diskuterar vi det. Se till att följa kodstandarden innan du pushar din PR.

## 📄 Licens
Detta projekt är öppen källkod och släpps under [MIT-licensen](https://opensource.org/licenses/MIT). Du får använda, modifiera och distribuera koden fritt, även i kommersiella projekt.