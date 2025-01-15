<?php 

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\User;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\UserProfileType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_index')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        /** @var PasswordAuthenticatedUserInterface $user */
        $user = $this->getUser();
        $userRepository = $entityManager->getRepository(User::class);
        $recipeRepository = $entityManager->getRepository(Recipe::class);
        $commentRepository = $entityManager->getRepository(Comment::class);

        // Create and handle the profile form
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
            return $this->redirectToRoute('admin_index');
        }

        return $this->render('auth/admin_index.html.twig', [
            'users' => $userRepository->findAll(),
            'recipes' => $recipeRepository->findAll(),
            'users_count' => count($userRepository->findAll()),
            'recipes_count' => count($recipeRepository->findAll()),
            'comments_count' => count($commentRepository->findAll()),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/recipe/{id}/delete', name: 'admin_recipe_delete', methods: ['POST'])]
    public function deleteRecipe(Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($recipe);
        $entityManager->flush();

        $this->addFlash('success', 'Recipe deleted successfully.');
        return $this->redirectToRoute('admin_index');
    }

    #[Route('/user/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'You cannot delete your own account.');
            return $this->redirectToRoute('admin_index');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'User deleted successfully.');
        return $this->redirectToRoute('admin_index');
    }

    #[Route('/user/{id}/toggle-role', name: 'admin_toggle_role', methods: ['POST'])]
    public function toggleAdminRole(User $user, EntityManagerInterface $entityManager): Response
    {
        $roles = $user->getRoles();
        
        if (in_array('ROLE_ADMIN', $roles)) {
            $roles = array_diff($roles, ['ROLE_ADMIN']);
        } else {
            $roles[] = 'ROLE_ADMIN';
        }
        
        $user->setRoles(array_values($roles));
        $entityManager->flush();

        $this->addFlash('success', 'User roles updated successfully.');
        return $this->redirectToRoute('admin_index');
    }
}