<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/api/register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {

        // Vérification du JSON envoyé
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse([
                'error' => 'Invalid or missing JSON body',
                'received' => $request->getContent()
            ], 400);
        }

        // Création du nouvel utilisateur
        $user = new User();
        $user->setEmail($data['email']);
        $user->setPseudoMinecraft($data['pseudoMinecraft']);
        $user->setUuidMinecraft($data['uuidMinecraft']);
        $user->setCredits(0);
        $user->setRoles(['ROLE_USER']);
        $user->setDateInscription(new \DateTimeImmutable());

        // Hash du mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // Sauvegarde en base
        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'User created'], 201);
    }

    #[Route('/api/login', methods: ['POST'])]
    public function login(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid or missing JSON body'], 400);
        }

        $user = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }

        // Génération du token
        $token = bin2hex(random_bytes(32));
        $user->setApiToken($token);
        $em->flush();

        return new JsonResponse(['token' => $token]);
    }

    #[Route('/api/me', methods: ['GET'])]
    public function me(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return new JsonResponse(['error' => 'Missing or invalid token'], 401);
        }

        $token = substr($authHeader, 7);

        $user = $em->getRepository(User::class)->findOneBy(['apiToken' => $token]);

        if (!$user) {
            return new JsonResponse(['error' => 'Invalid token'], 401);
        }

        return new JsonResponse([
            'email' => $user->getEmail(),
            'pseudoMinecraft' => $user->getPseudoMinecraft(),
            'uuidMinecraft' => $user->getUuidMinecraft(),
            'credits' => $user->getCredits(),
            'dateInscription' => $user->getDateInscription()->format('Y-m-d H:i:s'),
            'roles' => $user->getRoles(),
        ]);
    }
}
