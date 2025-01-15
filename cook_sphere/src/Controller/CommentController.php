<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[Route('/recipe/{id}/comments', name: 'recipe_comments')]
    public function index(Recipe $recipe, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setRecipe($recipe);
            $comment->setUser($this->getUser());
            
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('recipe_comments', ['id' => $recipe->getId()]);
        }

        $comments = $entityManager->getRepository(Comment::class)->findBy(
            ['recipe' => $recipe],
            ['createdAt' => 'DESC']
        );

        return $this->render('comment/index.html.twig', [
            'recipe' => $recipe,
            'comments' => $comments,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/comment/{id}/delete', name: 'comment_delete')]
    public function delete(Comment $comment, EntityManagerInterface $entityManager): Response
    {
        // Check if the current user is the comment author
        if ($comment->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot delete this comment.');
        }

        $recipeId = $comment->getRecipe()->getId();
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute('recipe_comments', ['id' => $recipeId]);
    }
} 