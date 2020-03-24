<?php
namespace App\Service;
use Symfony\Component\HttpFoundation\RequestStack;

// Un service pour manipuler le contenu de la Boutique
//  qui est composée de catégories et de produits stockés "en dur"
class BoutiqueService {

    // renvoie toutes les catégories
    public function findAllCategories() {
        return $this->categories;
    }

    // renvoie la categorie dont id == $idCategorie
    public function findCategorieById(int $idCategorie) {
        $res = array_filter($this->categories,
            function ($c) use($idCategorie) {
                return $c["id"] == $idCategorie;
            });
        return (sizeof($res) === 1) ? $res[array_key_first($res)] : null;
    }

    // renvoie le produits dont id == $idProduit
    public function findProduitById(int $idProduit) {
        $res = array_filter($this->produits,
            function ($p) use($idProduit) {
                return $p["id"] == $idProduit;
            });
        return (sizeof($res) === 1) ? $res[array_key_first($res)] : null;
    }

    // renvoie tous les produits dont idCategorie == $idCategorie
    public function findProduitsByCategorie(int $idCategorie) {
        return array_filter($this->produits,
            function ($p) use($idCategorie) {
                return $p["idCategorie"] == $idCategorie;
            });
    }

    // renvoie tous les produits dont libelle ou texte contient $search
    public function findProduitsByLibelleOrTexte(string $search) {
        return array_filter($this->produits,
            function ($p) use ($search) {
                return ($search=="" || mb_strpos(mb_strtolower($p["libelle"]." ".$p["texte"]), mb_strtolower($search)) !== false);
            });
    }

    // constructeur du service : injection des dépendances et tris
    public function __construct(RequestStack $requestStack) {
        // Injection du service RequestStack
        //  afin de pouvoir récupérer la "locale" dans la requête en cours
        $this->requestStack = $requestStack;
        // On trie le tableau des catégories selon la locale
        usort($this->categories, function ($c1, $c2) {
            return $this->compareSelonLocale($c1["libelle"], $c2["libelle"]);
        });
        // On trie le tableau des produits de chaque catégorie selon la locale
        usort($this->produits, function ($c1, $c2) {
            return $this->compareSelonLocale($c1["libelle"], $c2["libelle"]);
        });
    }

    ////////////////////////////////////////////////////////////////////////////

    private function compareSelonLocale(string $s1, $s2) {
        $collator=new \Collator($this->requestStack->getCurrentRequest()->getLocale());
        return collator_compare($collator, $s1, $s2);
    }

    private $requestStack; // Le service RequestStack qui sera injecté
    // Le catalogue de la boutique, codé en dur dans un tableau associatif
    private $categories = [
        [
            "id" => 1,
            "libelle" => "Rouge",
            "visuel" => "images/vin_rouge.jpg",
            "texte" => "Si vous lisez ces mots, il y a fort à parier que vous faites partie de ceux qui lèvent volontiers le coude pour apprécier un bon verre de vin rouge ! Vous connaissez la maxime, « Blanc sur rouge, rien ne bouge, rouge sur blanc, tout fout le camp ! »… Mais pour les amoureux de tannins, la question ne se pose pas : L’ordre de service se résume simplement par l’envie de partager un bon moment, un verre de vin à la main. Et oui, pour les épicuriens épris de plaisirs culinaires, le rouge passe avant tout !",
        ],
        [
            "id" => 2,
            "libelle" => "Blanc",
            "visuel" => "images/vin_blanc.jpg",
            "texte" => "Une envie de fraîcheur ? C’est un vin blanc qu’il vous faut ! Vin blanc sec, vin blanc sucré plutôt moelleux ou bien liquoreux, que vous l'aimiez fruité, floral, végétal, minéral, boisé ou épicé, ce qui est sûr, c'est qu'il aura du bouquet ! Pour savoir lequel choisir, Vinatis vous guide avec cette sélection des plus rafraîchissantes !",
        ],
        [
            "id" => 3,
            "libelle" => "Rosé",
            "visuel" => "images/vin_rose.jpg",
            "texte" => "Beaucoup de personnes imaginent que le vin rosé est constitué à base d'un mélange de vin rouge et de vin blanc. QUE NENNI !"],
    ];
    private $produits = [
        [
            "id" => 1,
            "idCategorie" => 1,
            "libelle" => "Pomme",
            "texte" => "Elle est bonne pour la tienne",
            "visuel" => "images/wine_1.png",
            "prix" => 3.42
        ],
        [
            "id" => 2,
            "idCategorie" => 1,
            "libelle" => "Poire",
            "texte" => "Ici tu n'en es pas une",
            "visuel" => "images/wine_1.png",
            "prix" => 2.11
        ],
        [
            "id" => 3,
            "idCategorie" => 1,
            "libelle" => "Pêche",
            "texte" => "Elle va te la donner",
            "visuel" => "images/wine_1.png",
            "prix" => 2.84
        ],
        [
            "id" => 4,
            "idCategorie" => 2,
            "libelle" => "CORTON CHARLEMAGNE GRAND CRU 2015 - LOUIS LATOUR",
            "texte" => "Riche, élégant et massif, les adeptes des grands vins bourguignons seront conquis !
Ce vin présente un bouquet riche d’arômes de citron avec des fruits tropicaux. Il est intensément puissant et emplit la bouche de multiples arômes qui nous guident vers une fin de bouche fine, minérale et qui se prolonge pendant plusieurs minutes. Il s’épanouira encore pendant plus de 20 ans. Un vin concentré, d'une persistance en bouche exemplaire !",
            "visuel" => "images/wine_2.png",
            "prix" => 2.90
        ],
        [
            "id" => 5,
            "idCategorie" => 2,
            "libelle" => "MEURSAULT - RÉSERVE PERSONNELLE 2018 - AEGERTER PÈRE ET FILS",
            "texte" => "Un parcours sans faute lors de sa dégustation à l'aveugle ! Chaque millésime, c'est NOTRE valeur sûre en Meursault...
Un nez frais et minéral, exaltant des parfums de fruité vanillé rehaussés d’un boisé fin encore perceptible, une bouche dévoilant une texture pleine et une évolution grasse. Ce Meursault aura convaincu grâce à sa matière ample et généreuse et son allonge équilibrée, fine et noisettée. Déjà très avenant, il est promis à un très bel avenir !",
            "visuel" => "images/wine_2.png",
            "prix" => 39.90
        ],
        [
            "id" => 6,
            "idCategorie" => 2,
            "libelle" => "PREMIERES GRIVES 2018 - DOMAINE TARIQUET",
            "texte" => "Dangereusement bon, cette cuvée a fait la notoriété et le succès du Domaine Tariquet. Ne pas le connaître est un péché... 
Les Premières Grives ou le plaisir de se faire plaisir... ! Succès mondial, les \" Premières Grives \" du Domaine du Tariquet c'est avant tout : \"une bouche gourmande, fruitée , vive et moelleuse (fruits exotiques, agrumes et raisins frais).\" C'est un vin qui se glisse entre 2 sensations : celle d'une onctuosité enjôleuse, conjuguée à une profonde fraîcheur. Ce duo moelleux/fraîcheur, qui fonctionne à merveille, lui donne cette originalité si recherchée. Il se boit et se partage en toutes occasions, avec des atouts si redoutables qu'à l'aveugle, on ne peut se douter de son formidable rapport qualité-prix...! Dangereusement bon, cette cuvée a fait la notoriété et le succès du Domaine Tariquet. Ne pas le connaître est un péché...!",
            "visuel" => "images/wine_2.png",
            "prix" => 10.90
        ],
        [
            "id" => 7,
            "idCategorie" => 3,
            "libelle" => "DUNE GRIS DE GRIS 2019",
            "texte" => "Dune c'est un rosé qui rend hommage au terroir maritime, d'où son nom qui fait référence au sable dans lequel les vignes sont implantées. Ce rosé plein de fraicheur et de finesse dévoile le savoir faire des vignerons qui ont su dompter ce terroir délicat et unique. Voici une appellation encore peu connue mais qui devient de plus en plus tendance ! Vous verrez à ce prix vous en redemanderez !! Soyez les premiers : foncez !",
            "visuel" => "images/wine_3.png",
            "prix" => 7.90
        ],
        [
            "id" => 8,
            "idCategorie" => 3,
            "libelle" => "CHATEAU ROMASSAN 2018 - DOMAINES OTT",
            "texte" => "L'élite des rosés. Récolté à la main, tri très sélectif, vieillissement en foudre de chêne. Célèbre entre tous, le rosé Coeur de grain séduit depuis les années 30 : aromatique, souple, nerveux et élégant. Idéal sur des crevettes flambées, une viande rouge très tendre ou un filet de poisson, ce Bandol inégalable reste LA référence des vins de Bandol.",
            "visuel" => "images/wine_3.png",
            "prix" => 24.90
        ],
        [
            "id" => 9,
            "idCategorie" => 3,
            "libelle" => "CUVEE ROSE & OR 2018 - CHATEAU MINUTY",
            "texte" => "Un rosé de grande classe à réserver pour les grands moments ! Précis, élégant et fin, il ne déçoit jamais
Elégant dans sa bouteille fine et épurée, il laisse entrevoir un rosé pâle et délicat. A la dégustation, on s’aperçoit rapidement qu’il tient toutes ses promesses tant il se veut expressif et concentré d’arômes gourmands qui laissent la part belle aux fruits. Une merveille de fraîcheur, un modèle d’élégance : Minuty est décidemment toujours au meilleur de son niveau !",
            "visuel" => "images/wine_3.png",
            "prix" => 23.90
        ],
    ];
}
