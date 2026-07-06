<?php 


namespace App\DTO;


use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class  RecipeInputDTO
{

    #[Assert\NotBlank(message:'Write name off recipe')]
    public ?string $title = null;

    #[Assert\NotBlank]    
    public ?string $instructions = null;


    #[Assert\NotBlank]
    #[Assert\Positive]    
    public ?int $baseServings = null;

    public ?string $mealType = null;
    public ?string $regionName = null;

    public ?array $ingredients = [];

   #[Assert\File(
    maxSize: '2M',
    mimeTypes: ['image/jpeg','image/png','image/webp'],
    mimeTypesMessage: 'Please enter a valid format for the image (JPEG, PNG, WEBP, SVG).'
   )]
    public ?UploadedFile $image = null;

}