<?php
namespace App\Controller;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Service\BoutiqueService;
use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BoutiqueController extends AbstractController
{
    public function shop($categorie, BoutiqueService $shop) {
        return$this->render('shop.html.twig');
    }

    public function wines(BoutiqueService $shop) {
        $categories = $this->getDoctrine()->getManager()->getRepository(Categorie::class)->findAll();
        //$categories = $shop->findAllCategories();
        return$this->render('wines.html.twig', [
            'categories' => $categories
        ]);
    }

    public function product($categorie) {
        $cat = $this->getDoctrine()->getManager()->getRepository(Categorie::class)->find($categorie);
        $wines = $cat->getArticles();
        //$wines = $shop->findProduitsByCategorie($categorie);
        return$this->render('product.html.twig', [
            'wines' => $wines,
            'categorie' => $categorie
        ]);
    }

    public function single_product($categorie, $product, BoutiqueService $shop) {
        $single = $this->getDoctrine()->getManager()->getRepository(Article::class)->find($product);
        //$single = $shop->findProduitById($product);
        return$this->render('single.html.twig', [
            'single' => $single,
            'categorie' => $categorie,
            'product' => $product
        ]);
    }

    public function add_panier($product, PanierService $panier, BoutiqueService $shop) {
        $product = $shop->findProduitById($product);
        $panier->ajouterProduit();
        $panier = $shop->findProduitById($product);
        return$this->render('single.html.twig', [
            'single' => $single,
        ]);
    }
}