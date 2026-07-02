<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\Region;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:seed-data',
    description: 'Seed DB with dummy data',
)]
class SeedDataCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Check/Create Chef
        $userRepository = $this->entityManager->getRepository(User::class);
        $chef = $userRepository->findOneBy(['email' => 'chef@example.com']);

        if (!$chef) {
            $chef = new User();
            $chef->setEmail('chef@example.com');
            $chef->setName('Gennaro Contaldo');
            $chef->setRoles(['ROLE_CHEF']);
            $chef->setPhone('1234567890');
            $chef->setGender('Male');
            $chef->setAge(60);
            $chef->setCity('Amalfi');
            $chef->setState('Campania');

            $hashedPassword = $this->passwordHasher->hashPassword($chef, 'password123');
            $chef->setPassword($hashedPassword);

            $this->entityManager->persist($chef);
            $io->info('Created Chef Gennaro');
        }

        // Define Cuisines and Recipes data
        $cuisinesData = [
            'Italian' => [
                [
                    'title' => 'Spaghetti Carbonara',
                    'instructions' => "1. Boil spaghetti in salted water.\n2. Fry pancetta in a pan until crispy.\n3. Whisk egg yolks and Pecorino Romano in a bowl.\n4. Toss hot pasta with pancetta, remove from heat, quickly stir in egg mixture, and serve with freshly cracked black pepper.",
                    'baseServings' => 4,
                    'ingredients' => [
                        ['Spaghetti', 400, 'g'],
                        ['Pancetta', 150, 'g'],
                        ['Egg yolks', 4, 'pcs'],
                        ['Pecorino Romano', 50, 'g'],
                        ['Black pepper', 1, 'tsp'],
                    ]
                ],
                [
                    'title' => 'Classic Margherita Pizza',
                    'instructions' => "1. Preheat oven to maximum temperature.\n2. Roll out pizza dough.\n3. Spread tomato sauce, mozzarella cheese, and fresh basil leaves.\n4. Bake for 10-15 minutes until cheese is bubbly and crust is golden brown.",
                    'baseServings' => 2,
                    'ingredients' => [
                        ['Pizza dough', 1, 'pc'],
                        ['Tomato sauce', 100, 'ml'],
                        ['Fresh mozzarella', 150, 'g'],
                        ['Fresh basil leaves', 6, 'pcs'],
                        ['Olive oil', 1, 'tbsp'],
                    ]
                ]
            ],
            'Indian' => [
                [
                    'title' => 'Butter Chicken',
                    'instructions' => "1. Marinate chicken in yogurt and spices.\n2. Grill or pan-sear chicken until cooked.\n3. Make gravy with tomatoes, butter, heavy cream, and cashew paste.\n4. Add chicken to the gravy and simmer for 10 minutes.\n5. Garnish with kasuri methi and cream.",
                    'baseServings' => 4,
                    'ingredients' => [
                        ['Chicken thighs', 800, 'g'],
                        ['Yogurt', 100, 'g'],
                        ['Butter', 50, 'g'],
                        ['Tomato puree', 400, 'g'],
                        ['Heavy cream', 100, 'ml'],
                        ['Garam Masala', 2, 'tsp'],
                    ]
                ],
                [
                    'title' => 'Paneer Tikka',
                    'instructions' => "1. Cut paneer and bell peppers into cubes.\n2. Marinate in yogurt, gram flour, and spices.\n3. Skewer paneer and vegetables.\n4. Grill in oven or tandoor until charred around edges.\n5. Serve hot with mint chutney.",
                    'baseServings' => 3,
                    'ingredients' => [
                        ['Paneer (Cottage cheese)', 300, 'g'],
                        ['Bell Peppers', 2, 'pcs'],
                        ['Onions', 2, 'pcs'],
                        ['Yogurt marinade', 150, 'g'],
                        ['Lemon juice', 1, 'tbsp'],
                    ]
                ]
            ],
            'Mexican' => [
                [
                    'title' => 'Classic Beef Tacos',
                    'instructions' => "1. Cook ground beef in a skillet, drain fat.\n2. Stir in taco seasoning and water, simmer for 5 minutes.\n3. Warm taco shells.\n4. Assemble tacos with beef, lettuce, cheese, and tomatoes.",
                    'baseServings' => 3,
                    'ingredients' => [
                        ['Ground beef', 500, 'g'],
                        ['Taco shells', 6, 'pcs'],
                        ['Shredded lettuce', 1, 'cup'],
                        ['Cheddar cheese', 100, 'g'],
                        ['Taco seasoning', 1, 'pack'],
                    ]
                ]
            ],
            'Chinese' => [
                [
                    'title' => 'Kung Pao Chicken',
                    'instructions' => "1. Marinate chicken cubes with soy sauce and cornstarch.\n2. Fry dried chilis and Sichuan peppercorns in wok.\n3. Add chicken, garlic, and ginger, stir-fry until cooked.\n4. Toss in peanuts and green onions with sauce, stir-fry until thick.",
                    'baseServings' => 2,
                    'ingredients' => [
                        ['Chicken breast', 400, 'g'],
                        ['Peanuts', 50, 'g'],
                        ['Soy sauce', 2, 'tbsp'],
                        ['Dried red chili', 6, 'pcs'],
                        ['Garlic cloves', 4, 'pcs'],
                    ]
                ]
            ],
            'American' => [
                [
                    'title' => 'Classic Cheeseburger',
                    'instructions' => "1. Shape ground beef into patties, season with salt and pepper.\n2. Grill patties for 4 minutes per side, placing cheese slice during the last minute.\n3. Toast burger buns.\n4. Assemble with patties, lettuce, tomato, and burger sauce.",
                    'baseServings' => 2,
                    'ingredients' => [
                        ['Beef patties', 2, 'pcs'],
                        ['Burger buns', 2, 'pcs'],
                        ['Cheddar cheese slices', 2, 'pcs'],
                        ['Lettuce leaves', 2, 'pcs'],
                        ['Tomato slices', 4, 'pcs'],
                    ]
                ]
            ]
        ];

        $regionRepository = $this->entityManager->getRepository(Region::class);
        $recipeRepository = $this->entityManager->getRepository(Recipe::class);

        foreach ($cuisinesData as $cuisineName => $recipes) {
            // Find or create Region
            $region = $regionRepository->findOneBy(['name' => $cuisineName]);
            if (!$region) {
                $region = new Region();
                $region->setName($cuisineName);
                $this->entityManager->persist($region);
                $io->info("Created Region: {$cuisineName}");
            }

            foreach ($recipes as $recipeData) {
                $recipe = $recipeRepository->findOneBy(['title' => $recipeData['title']]);
                if (!$recipe) {
                    $recipe = new Recipe();
                    $recipe->setTitle($recipeData['title']);
                    $recipe->setInstructions($recipeData['instructions']);
                    $recipe->setBaseServings($recipeData['baseServings']);
                    $recipe->setChef($chef);
                    $recipe->setRegion($region);
                    $recipe->setImage(null);
                    $this->entityManager->persist($recipe);

                    foreach ($recipeData['ingredients'] as $ing) {
                        $ingredient = new Ingredient();
                        $ingredient->setName($ing[0]);
                        $ingredient->setBaseQuantity($ing[1]);
                        $ingredient->setUnit($ing[2]);
                        $ingredient->setRecipe($recipe);
                        $this->entityManager->persist($ingredient);
                    }
                    $io->info("Created Recipe: {$recipeData['title']}");
                }
            }
        }

        $this->entityManager->flush();
        $io->success('Database seeded successfully!');

        return Command::SUCCESS;
    }
}
