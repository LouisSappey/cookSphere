<?php
namespace App\Controller;

use App\Entity\Recipe;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProfileController extends AbstractController
{
    #[Route('/profile', name: 'user_profile')]
    public function profile(
        Request $request, 
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('page_login');
        }

        // Get user's recipes
        $recipes = $entityManager->getRepository(Recipe::class)->findBy(['author' => $user]);
        
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle password change if provided
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Profile updated successfully.');
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('auth/user_profile.html.twig', [
            'form' => $form->createView(),
            'recipes' => $recipes
        ]);
    }
} 