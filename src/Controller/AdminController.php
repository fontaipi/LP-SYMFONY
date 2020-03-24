<?php

namespace App\Controller;

use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    public function index(PanierService $panierService) {
        return $this->render('backoffice.html.twig');
    }
}
