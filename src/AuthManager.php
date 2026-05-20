<?php 

namespace Raktfranhjartat\SmallMvcAuth;

class AuthManager
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function attempt(string $username, string $password, $addCookie = false): bool
    {
        $user = $this->db->query("SELECT * FROM users WHERE username = ?", [$username])->fetch(\PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            if ($addCookie) {
                $token = bin2hex(random_bytes(32)); 
                
                $this->db->query("UPDATE users SET remember_token = ? WHERE id = ?", [$token, $user['id']]);
                
                setcookie('remember_token', $token, time() + (86400 * 30), '/', '', false, true);
            }
            return true;
        }

        return false;
    }


    public function check(): bool
    {
        if (isset($_SESSION['user_id'])) {
            return true;
        }

        if (isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            
            $user = $this->db->query("SELECT id FROM users WHERE remember_token = ?", [$token])->fetch(\PDO::FETCH_ASSOC);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                return true;
            }
        }

        return false;
    }

    public function requireLogin(string $redirectTo = '/login'): void
    {
        if (!$this->check()) {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];

            header('Location: ' . $redirectTo);
            exit;
        }
    }

    public function intendedUrl(string $default = '/'): string
    {
        if (isset($_SESSION['intended_url'])) {
            $url = $_SESSION['intended_url'];
            unset($_SESSION['intended_url']);
            return $url;
        }
        return $default;
    }
}