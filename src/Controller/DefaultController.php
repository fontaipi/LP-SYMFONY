<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function index() {
        return $this->render('home.html.twig');
    }

    public function contact() {
        return $this->render('wines.html.twig');
    }
}