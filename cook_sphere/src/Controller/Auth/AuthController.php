<?php 

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Mailer\MailerInterface;

class AuthController extends AbstractController
{
    #[Route(path: '/login', name: 'page_login')]
    public function login(): Response
    {
        return $this->render('auth/login.html.twig');
    }

    #[Route(path: '/forgot-password', name: 'page_forgot_password')]
    public function forgotPassword(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $email = $request->get('_email');
        
        // Vérification si un email est soumis
        if ($request->isMethod('POST') && !$email) {
            $this->addFlash('error', 'Veuillez fournir une adresse email.');
            return $this->render('auth/forgot.html.twig');
        }
        
        if ($email) {
            $user = $userRepository->findOneBy(['email' => $email]);
            
            if (!$user) {
                $this->addFlash('error', 'Aucun utilisateur trouvé avec cette adresse email.');
                return $this->render('auth/forgot.html.twig');
            }
    
            $resetToken = Uuid::v4()->toRfc4122();
            $user->setResetPasswordToken($resetToken);
            $entityManager->persist($user);
            $entityManager->flush();
    
            $emailMessage = (new \Symfony\Bridge\Twig\Mime\TemplatedEmail())
                ->from('louis.sappey@hotmail.com')
                ->to($user->getEmail())
                ->subject('Réinitialisation de votre mot de passe')
                ->htmlTemplate('email/reset.html.twig')
                ->context([
                    'resetToken' => $resetToken,
                    'userEmail' => $user->getEmail(),
                    'resetUrl' => $this->generateUrl('page_reset_password', [
                        'token' => $resetToken,
                    ], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
                ]);
    
            try {
                $mailer->send($emailMessage);
                $this->addFlash('success', 'Un lien de réinitialisation a été envoyé à votre adresse email.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi de l\'email : ' . $e->getMessage());
            }
    
            return $this->redirectToRoute('page_forgot_password');
        }
    
        return $this->render('auth/forgot.html.twig');
    }

    #[Route(path: '/reset-password/{token}', name: 'page_reset_password')]
    public function resetPassword(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $userRepository->findOneBy(['resetPasswordToken' => $token]);
    
        if (!$user) {
            $this->addFlash('error', 'Jeton invalide ou utilisateur introuvable.');
            return $this->redirectToRoute('page_forgot_password');
        }
    
        if ($request->isMethod('POST')) {
            $newPassword = $request->get('password');
            $confirmPassword = $request->get('repeat-password');
    
            if ($newPassword !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->render('auth/reset.html.twig', ['token' => $token]);
            }
    
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $newPassword)) {
                $this->addFlash(
                    'error',
                    'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.'
                );
                return $this->render('auth/reset.html.twig', ['token' => $token]);
            }
    
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    
            $user->setPassword($hashedPassword);
            $user->setResetPasswordToken(null);
    
            $entityManager->persist($user);
            $entityManager->flush();
    
            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès. Veuillez vous connecter.');
            return $this->redirectToRoute('page_login');
        }
    
        return $this->render('auth/reset.html.twig', ['token' => $token]);
    }

    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('index.html.twig');
    }
}
