<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Utility\Upload;
use \Core\View;
use TypeError;

/**
 * Product controller
 */
class Product extends \Core\Controller
{

    /**
     * Affiche la page d'ajout
     * @return void
     */
    public function indexAction()
    {

        if (isset($_POST['submit'])) {

            try {
                $f = $_POST;
                // Done: Validation
                if ($f['title'] == "" or $f['description'] == "" or $f['city'] == "" or $f['picture'] == "") {
                    throw new \Exception("Empty Fields, fill all the required fields");
                }

                $f['user_id'] = $_SESSION['user']['id'];
                $id = Articles::save($f);

                $pictureName = Upload::uploadFile($_FILES['picture'], $id);

                Articles::attachPicture($id, $pictureName);

                header('Location: /product/' . $id);
            } catch (\Exception $e) {
                var_dump($e);
            }
        }

        View::renderTemplate('Product/Add.html');
    }

    /**
     * Affiche la page d'un produit
     * @return void
     */
    public function showAction()
    {
        $id = $this->route_params['id'];

        try {
            Articles::addOneView($id);
            $suggestions = Articles::getSuggest();
            $article = Articles::getOne($id);
        } catch (\Exception $e) {
            var_dump($e);
        }

        View::renderTemplate('Product/Show.html', [
            'article' => $article[0],
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Affiche le formulaire de contact pour un produit
     * @return void
     */
    public function contactAction()
    {   
        $product_id = $this->route_params['id'];
        try {
            Articles::addOneView($product_id);
            $article = Articles::getOne($product_id);
        } catch (\Exception $e) {
            var_dump($e);
        }
        
        if (isset($_POST['submit'])) {

            try {
                $f = $_POST;
                // Done: Validation
                if ($f['message'] == "") {
                    throw new \Exception("Empty Fields, fill all the required fields");
                }
                
                header('Location: /product/' . $product_id);
            } catch (\Exception $e) {
                var_dump($e);
            }
        }

        View::renderTemplate('Product/Contact.html', [
            'contacted_username' => $article[0]['username'],
            'article' => $article[0]['name'],
        ]);
    }


}
