<?php

namespace Core;

/**
 * View
 *
 * PHP version 7.0
 */
class View
{

    /**
     * Render a view file
     *
     * @param string $view  The view file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = dirname(__DIR__) . "/App/Views/$view";  // relative to Core directory

        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("$file not found");
        }
    }

    /**
     * Render a view template using Twig
     *
     * @param string $template  The template file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
public static function renderTemplate($template, $args = [])
{
    static $twig = null;

    if ($twig === null) {
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/App/Views');
        $twig = new \Twig\Environment($loader, [
            'debug' => true,
            'strict_variables' => true,
            'auto_reload' => true,
        ]);
        $twig->addExtension(new \Twig\Extension\DebugExtension());
    }

    try {
        echo $twig->render($template, View::setDefaultVariables($args));
    } catch (\Throwable $e) {
        echo "<pre style='color:red; font-weight: bold;'>TWIG ERROR : " . $e->getMessage() . "</pre>";
        exit;
    }
}

    /**
     * Ajoute les données à fournir à toutes les pages
     * @param array $args
     * @return array
     */
    public static function setDefaultVariables($args = []){

        $args["user"] = isset($_SESSION['user']) ? $_SESSION['user'] : null;

        return $args;
    }
}
