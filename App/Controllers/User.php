<?php

namespace App\Controllers;

use App\Config;
use App\Model\UserRegister;
use App\Models\Articles;
use App\Utility\Hash;
use \Core\View;
use Exception;
use http\Env\Request;
use http\Exception\InvalidArgumentException;

/**
 * User controller
 */
class User extends \Core\Controller
{

    /**
     * Affiche la page de login
     */
    public function loginAction()
    {
        if (isset($_POST['submit'])) {
            $f = $_POST;

            // D: Validation
            if ($this->login($f)){
                header('Location: /account');
            }
            // Si login OK, redirige vers le compte
            
        }

        View::renderTemplate('User/login.html');
    }
    private function login($data)
    {
        try {
            echo "Tentative de login...<br>";

            if (!isset($data['email']) || !isset($data['password'])) {
                echo "Champs manquants<br>";
                return false;
            }

            echo "Email reçu : " . $data['email'] . "<br>";
            $user = \App\Models\User::getByLogin($data['email']);

            if (!$user) {
                echo "Utilisateur non trouvé<br>";
                return false;
            }

            echo "Utilisateur trouvé :<pre>";
            print_r($user);
            echo "</pre>";

            $hashed = Hash::generate($data['password'], $user['salt']);
            echo "Hash généré : $hashed<br>";

            if ($hashed !== $user['password']) {
                echo "Mot de passe incorrect<br>";
                return false;
            }

            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'is_admin' => $user['is_admin'],
            ];

            echo "<pre>SESSION : ";
            print_r($_SESSION);
            echo "</pre>";
            return true;

        } catch (Exception $ex) {
            echo "Exception : " . $ex->getMessage();
            return false;
        }
    }

    
    /**
     * Page de création de compte
     */
    public function registerAction()
    {
        if (isset($_POST['submit'])) {
            $f = $_POST;

            // validation
            if ($f['password'] == $f['password-check']) {
                $this->register($f);
                // DONE: Callback the login function to connect the user
                $this->login($f);
                header('Location: /');
                // DONE: Gestion d'erreur côté utilisateur
            }
            throw new Exception('Les mots de passe ne correspondent pas.');          
        }

        View::renderTemplate('User/register.html');
    }


    /*
     * Fonction privée pour enregister un utilisateur
     */
    private function register($data)
    {
        try {
            // Generate a salt, which will be applied to the during the password
            // hashing process.
            $salt = Hash::generateSalt(32);

            $userID = \App\Models\User::createUser([
                "email" => $data['email'],
                "username" => $data['username'],
                "password" => Hash::generate($data['password'], $salt),
                "salt" => $salt
            ]);

            return $userID;

        } catch (Exception $ex) {
            // TODO : Set flash if error : utiliser la fonction en dessous
            /* Utility\Flash::danger($ex->getMessage());*/
        }
    }


    /**
     * Affiche la page du compte
     */
    public function accountAction()
    {
        $articles = Articles::getByUser($_SESSION['user']['id']);

        View::renderTemplate('User/account.html', [
            'articles' => $articles
        ]);
    }


    /**
     * Logout: Delete cookie and session. Returns true if everything is okay,
     * otherwise turns false.
     * @access public
     * @return boolean
     * @since 1.0.2
     */
    public function logoutAction()
    {

        /*
        if (isset($_COOKIE[$cookie])){
            // TODO: Delete the users remember me cookie if one has been stored.
            // https://github.com/andrewdyer/php-mvc-register-login/blob/development/www/app/Model/UserLogin.php#L148
        }*/
        // Destroy all data registered to the session.

        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                null,
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        header("Location: /");
        session_destroy();

        exit;
    }

    private function checkAdminAccess(): void
    {
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['is_admin']) || $_SESSION['user']['is_admin'] != 1) {
            header('Location: /');
            exit;
        }
    }

    public function adminStats()
    {
        $this->checkAdminAccess();

        $userModel = new \App\Models\User();
        $userCount = $userModel->countAll();
        $articleCount = Articles::countAll();
        $articlesPerMonth = Articles::getArticlesPerMonth();

        $stats = [
            'user_count' => $userCount,
            'article_count' => $articleCount,
            'articles_per_month' => $articlesPerMonth
        ];

        try {
            View::renderTemplate('User/admin-stats.html', ['stats' => $stats]);
        } catch (\Throwable $e) {
            echo "<pre style='color:red;'>TWIG ERROR : " . $e->getMessage() . "</pre>";
            exit;
        }
    }

}
