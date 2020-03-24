<?php

// src/Service/PanierService.php
namespace App\Service;
use App\Entity\Article;
use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Usager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\BoutiqueService;
// Service pour manipuler le panier et le stocker en session
class PanierService {
    ////////////////////////////////////////////////////////////////////////////
    const PANIER_SESSION = 'panier'; // Le nom de la variable de session du panier
    private $session;  // Le service Session
    private $boutique; // Le service Boutique
    private $panier;   // Tableau associatif idProduit => quantite
	                   //  donc $this->panier[$i] = quantite du produit dont l'id = $i
    // constructeur du service
    public function __construct(SessionInterface $session, BoutiqueService $boutique) {
        // Récupération des services session et BoutiqueService
        $this->boutique = $boutique;
        $this->session = $session;
        // Récupération du panier en session s'il existe, init. à vide sinon
        $this->panier = $session->get(self::PANIER_SESSION,[]);
    }
    // getContenu renvoie le contenu du panier
    //  tableau d'éléments [ "produit" => un produit, "quantite" => quantite ]
    public function getContenu() {
        $contenu = [];
        foreach ($this->panier as $id=>$quantite) {
            $element = [];
            $produit = $this->boutique->findProduitById($id);
            $element["produit"] = $produit;
            $element["quantite"] = $quantite;
            array_push($contenu, $element);
        }
        return $contenu;

    }
    // getTotal renvoie le montant total du panier
    public function getTotal(EntityManager $em) {
        $total = 0;
        foreach($this->session->get(self::PANIER_SESSION) as $key => $value ){
            $produitobj = $em->getRepository(Article::class)->find($key);
            $total += $value * $produitobj->getPrix();
        }
        return $total;
    }
    // getNbProduits renvoie le nombre de produits dans le panier
    public function getNbProduits() {
        return count($this->panier);
    }

    // ajouterProduit ajoute au panier le produit $idProduit en quantite $quantite 
    public function ajouterProduit(int $idProduit, int $quantite = 1) {
        if(isset($this->panier[$idProduit])){
            $this->panier[$idProduit] += $quantite;
        }
        else{
            $this->panier[$idProduit] = $quantite;
        }
        $this->session->set(self::PANIER_SESSION,$this->panier);
    }

    // enleverProduit enlève du panier le produit $idProduit en quantite $quantite 
    public function enleverProduit(int $idProduit, int $quantite = 1) {
        if(isset($this->panier[$idProduit])){
            if($this->panier[$idProduit]- $quantite <= 0){
                unset($this->panier[$idProduit]);
            }
            else{
                $this->panier[$idProduit] = $this->panier[$idProduit] - $quantite;
            }
        }
        $this->session->set(self::PANIER_SESSION,$this->panier);
    }
    // supprimerProduit supprime complètement le produit $idProduit du panier
    public function supprimerProduit(int $idProduit) {
        unset($this->panier[$idProduit]);
        $this->session->set(self::PANIER_SESSION,$this->panier);
    }
    // vider vide complètement le panier
    public function vider() {
        $this->panier = [];
        $this->session->set(self::PANIER_SESSION,$this->panier);
    }

    public function panierToCommande(Usager $user, EntityManager $em){
        $commande = new Commande();
        if(count($this->session->get(self::PANIER_SESSION)) > 0){
            $commande->setDateCommande(new \DateTime());
            $commande->setStatut("Fini");
            $commande->setIdUsager($user);
            $em->persist($commande);
            $em->flush();
            foreach($this->session->get(self::PANIER_SESSION) as $key => $value ){
                $produitobj = $em->getRepository(Article::class)->find($key);
                $ligneCommande = new LigneCommande();
                $ligneCommande->setIdArticle($produitobj);
                $ligneCommande->setIdCommande($commande);
                $ligneCommande->setPrix( ($produitobj->getPrix() * $value ));
                $ligneCommande->setQuantite($value);
                $em->persist($ligneCommande);
                $em->flush();
            }
        }

        $this->session->set(self::PANIER_SESSION,[]);
        return $commande;

    }
}
