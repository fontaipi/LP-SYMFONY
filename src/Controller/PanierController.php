<?php
namespace App\Controller;
use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Usager;
use App\Service\BoutiqueService;
use App\Service\PanierService;
use PhpParser\Node\Scalar\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class PanierController extends AbstractController
{
    public function index(PanierService $panierService) {
        $contenu =$panierService->getContenu();
        $total = $panierService->getTotal($this->getDoctrine()->getManager());
        return $this->render('panier.html.twig', [
            'contenu'=>$contenu,
            'total'=>$total
        ]);
    }

    public function ajouter(PanierService $panierService,int $idProduit, String $quantite) {
        $quantite = (int)$quantite;
        $panierService->ajouterProduit($idProduit, $quantite);
        return $this->redirectToRoute('panier_index');
    }

    public function enlever() {
        return $this->render('Default/index.html.twig', [
        ]);
    }

    public function supprimer(PanierService $panierService,int $idProduit) {
        $panierService->supprimerProduit($idProduit);
        return $this->redirectToRoute('panier_index');
    }

    public function vider() {
        return $this->render('Default/index.html.twig', [
        ]);
    }

    public function valider(PanierService $panierService) {
        $commande = $panierService->panierToCommande($this->getUser(), $this->getDoctrine()->getManager());
        return $this->render('panier_validation.html.twig', [
            'commande'=> $commande
        ]);
    }
}
