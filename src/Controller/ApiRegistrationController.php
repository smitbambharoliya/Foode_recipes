<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

final class ApiRegistrationController extends AbstractController
{
    #[Route('/api/register', name: 'app_api_registration', methods:['POST'])]
    #[OA\Tag(name: 'Recipes(login)')]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
       if (empty($data['email']) || empty($data['password']) || empty($data['age']) || empty($data['phone']) || empty($data['gender']) || empty($data['city']) || empty($data['state']) || empty($data['country'])){
          return new JsonResponse(['error'=>'Email and password is not corete plse enter write password our email!'],Response::HTTP_BAD_REQUEST);
       }

       $user = new User();
       $user->setEmail($data['email']);
       $user->setName($data['name'] ?? '');
       $user->setRoles(['ROLE_USER']);
       $user->setAge($data['age']);
       $user->setPhone($data['phone']);
       $user->setGender($data['gender']);
       $user->setCity($data['city']);
       $user->setState($data['state']);
       $user->setCountry($data['country']);


       $hashedPassword = $passwordHasher->hashPassword($user,$data['password']);
       $user->setPassword($hashedPassword);


       $entityManager->persist($user);
       $entityManager->flush();
       
        
        return new JsonResponse(['message' => 'Registration is susccussfuly done New user login now!..'],Response::HTTP_CREATED);
    }
}
