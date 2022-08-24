<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $em;
    private $userRepository;
    private $passwordHasher;

    public function __construct(EntityManagerInterface      $entityManager,
                                UserRepository              $userRepository,
                                UserPasswordHasherInterface $passwordHasher
    )
    {
        $this->em = $entityManager;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/user', name: 'user_home')]
    public function indexAction()
    {
        $listUsers = $this->userRepository->findAll();

        return $this->render('User/index.html.twig', array(
            'listUsers' => $listUsers
        ));
    }

    #[Route('/user/view/{id}', name: 'user_view')]
    public function viewAction($id)
    {
        $user = $this->userRepository->find($id);

        return $this->render('User/view.html.twig', array(
            'user' => $user
        ));
    }

    #[Route('/user/add', name: 'user_add')]
    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            $this->em->persist($user);
            $this->em->flush();

            // TODO : envoyer le mail

            $request->getSession()->getFlashBag()->add('notice', 'Utilisateur bien enregistré.');

            return $this->redirect($this->generateUrl('user_view', array('id' => $user->getId())));
        }

        return $this->render('User/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    #[Route('/user/edit/{id}', name: 'user_edit')]
    public function editAction($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->userRepository->find($id);
        $user->setPassword("");

        if ($user == null) {
            throw $this->createNotFoundException("L'utilisateur d'id " . $id . " n'existe pas.");
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            $this->em->persist($user);
            $this->em->flush();

            // TODO : envoyer le mail

            $request->getSession()->getFlashBag()->add('notice', 'Utilisateur bien enregistré.');

            return $this->redirect($this->generateUrl('user_view', array('id' => $user->getId())));
        }

        return $this->render('User/edit.html.twig', array(
            'form' => $form->createView(), 'user' => $user
        ));
    }

    #[Route('/user/edit_profil', name: 'user_edit_profil')]
    public function editMyProfilAction(Request $request)
    {
        $user = $this->getUser();
        $user->setPassword("");

        if ($user == null) {
            throw $this->createNotFoundException("L'utilisateur d'id " . $id . " n'existe pas.");
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // TODO : Encoder le mot de passe

            $this->em->persist($user);
            $this->em->flush();

            // TODO : envoyer le mail

            $request->getSession()->getFlashBag()->add('notice', 'Utilisateur bien enregistré.');

            return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('User/edit_profil.html.twig', array(
            'form' => $form->createView(), 'user' => $user
        ));
    }

    #[Route('/user/delete/{id}', name: 'user_delete')]
    public function deleteAction($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw new NotFoundHttpException("L'utilisateur d'id " . $id . " n'existe pas.");
        }

        $userConnected = $this->getUser();
        if ($userConnected->getId() == $user->getId()) {
            throw new NotFoundHttpException("Vous ne pouvez pas supprimer votre propre compte.");
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->remove($user, true);

            $request->getSession()->getFlashBag()->add('info', "Le service a bien été supprimée.");

            return $this->redirect($this->generateUrl('service_home'));
        }

        return $this->render('User/delete.html.twig', array(
            'user' => $user,
            'form' => $form->createView()
        ));
    }
}