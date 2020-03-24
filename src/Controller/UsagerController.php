<?php

namespace App\Controller;

use App\Entity\Usager;
use App\Form\UsagerType;
use App\Repository\UsagerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsagerController extends AbstractController
{
    public function index(UsagerRepository $usagerRepository): Response
    {
        if ($this->getUser()){
            return $this->render('usager/index.html.twig', [
                'usager' => $usagerRepository->find($this->getUser()),
            ]);
        } else {
            return $this->render('home.html.twig');
        }
    }

    public function new(Request $request, SessionInterface $session,UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $usager = new Usager();
        $form = $this->createForm(UsagerType::class, $usager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $usager->setPassword($passwordEncoder->encodePassword($usager, $usager->getPassword()));
            $usager->setRoles(["ROLE_CLIENT"]);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($usager);
            $entityManager->flush();

            return $this->redirectToRoute('usager_accueil');
        }

        return $this->render('usager/new.html.twig', [
            'usager' => $usager,
            'form' => $form->createView(),
        ]);
    }
}
