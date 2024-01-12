<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\Type\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard/post')]
class PostController extends AbstractController
{

    #[Route('/create', name: 'post_create', methods: ['GET', 'POST'])]
    public function createPost(Request $request, EntityManagerInterface $manager)
    {
        # Création du formulaire à partir de mon modèle PostType
        $post = new Post();
        $post->setUser($this->getUser());

        # dump($post);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($post);
            $manager->flush();
        }

        return $this->render('post/create.html.twig', [
            'form' => $form
        ]);
    }
}