<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
final class UserController extends AbstractController
{
    #[Route('/home',name:"app_home")]
    public function index(Request $request,RecipeRepository $recipeRepository): Response
    {

        return $this->render('user/home.html.twig', [
            'controller_name' => 'UserController',
            'recipes'=>$recipeRepository->findAll(),
        ]);
    }

    #[Route('/home/{id}',name:'app_home_show')]
    public function show(RecipeRepository $recipeRepository,EntityManagerInterface $entityManager,int $id): Response
    {
        $recipe=$recipeRepository->find($id);

        if (!$recipe) {
            throw $this->createNotFoundException('No recipe found for id ' . $id);
        }


        $recipe->setViews($recipe->getViews()+1);
        $entityManager->persist($recipe);
        $entityManager->flush();


        return $this->render('user/home_show.html.twig',[
            'recipe'=>$recipe,
        ]);
    }
}
