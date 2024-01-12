<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * Permet l'inscription d'un nouvel utilisateur.
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    #[Route('/user/register',name: 'user_register', methods: ['GET', 'POST'])]
    public function register(Request $request,
                             EntityManagerInterface $manager,
                             UserPasswordHasherInterface $passwordHasher): Response
    {

        # Création d'un nouvel utilisateur
        $user = new User(['ROLE_USER']);

        # Création de notre formulaire d'inscription
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            # Hashage du mot de passe
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                )
            );

            # Enregistrement en BDD
            $manager->persist($user);
            $manager->flush();

            # Notification Flash
            $this->addFlash('success',
                'Félicitation, vous pouvez maintenant vous connecter !');

            # Redirection
            return $this->redirectToRoute('app_login');

        }

        # Passage du formulaire à la vue
        return $this->render('user/register.html.twig', [
            'form' => $form
        ]);
    }
}