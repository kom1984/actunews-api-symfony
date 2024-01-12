<?php

namespace App\Controller;


use App\Entity\Category;
use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function home(PostRepository $postRepository): Response
    {

        $posts = $postRepository->findAll();
        # dump($posts);

        # 1. Récupération des informations dans la BDD
        return $this->render('default/home.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * Afficher les articles d'une catégorie
     * ex. http://localhost:8000/politique
     * @param string $slug
     * @return Response
     */
    #[Route('/category/{slug<\w+>}', name: 'default_category', methods: ['GET'])]
    public function category(Category $category): Response
    {
        # $category = $categoryRepository->findOneBy(['slug' => $slug]);
        # $category = $categoryRepository->findOneBySlug($slug);
        # $posts = $category->getPosts();
        # dump($category, $posts);

        return $this->render('default/category.html.twig', [
            'category' => $category
        ]);
    }

    /**
     * Afficher un article
     * ex. http://localhost:8000/politique/slug-de-mon-article_86545.html
     * @param Post $post
     * @return Response
     */
    #[Route('/{categorySlug<\w+>}/{slug}_{id<\d+>}.html', name: 'default_post', methods: ['GET'])]
    public function post(Post $post): Response
    {
        return $this->render('default/post.html.twig', [
            'post' => $post
        ]);
    }
}