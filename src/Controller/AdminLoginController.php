<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class AdminLoginController extends AbstractController
{
    #[Route('/connect-admin', name: 'admin_jwt_login')]
    public function index(
        Request $request,
        JWTEncoderInterface $jwtEncoder,
        EntityManagerInterface $entityManager,
        Security $security
    ): Response {
        $token = $request->query->get('token');

        if (!$token) {
            $this->addFlash('error', 'Token manquant.');
            return $this->redirectToRoute('app_login');
        }

        try {
            // 1. Décoder le token
            $data = $jwtEncoder->decode($token);
            
            if (!$data) {
                throw new JWTDecodeFailureException(JWTDecodeFailureException::INVALID_TOKEN, 'Token invalide');
            }

            // 2. Récupérer l'utilisateur (username est souvent l'email ou le champ identifier)
            $username = $data['username'] ?? $data['email'] ?? null;

            if (!$username) {
                throw new \Exception('Token ne contient pas d\'identifiant utilisateur.');
            }

            $userRepository = $entityManager->getRepository(User::class);
            $user = $userRepository->findOneBy(['email' => $username]);

            if (!$user) {
                throw new UserNotFoundException(sprintf('User "%s" not found.', $username));
            }

            // 3. Connecter l'utilisateur manuellement sur le firewall 'main'
            // Le 3ème argument 'main' doit correspondre au nom de votre firewall dans security.yaml
            $security->login($user, 'form_login', 'main');

            // 4. Rediriger vers l'admin
            return $this->redirectToRoute('admin');

        } catch (JWTDecodeFailureException $e) {
            $this->addFlash('error', 'Token invalide ou expiré.');
            return $this->redirectToRoute('app_login');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur de connexion : ' . $e->getMessage());
            return $this->redirectToRoute('app_login');
        }
    }
}
