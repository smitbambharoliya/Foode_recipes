<?php 



namespace App\Service;





class RecipeDateTimeHelper
{
    public function getCurrentMealType():string
    {
        $hour = (int) (new \DateTime())->format('G');

        if ($hour>= 4 && $hour < 11){
            return 'Breakfast';
        }
        elseif ($hour>= 11 && $hour < 15){
            return 'Lunch';
        }
        elseif ($hour>= 15 && $hour < 19){
            return 'Snack';
        }
        elseif ($hour>=19 && $hour < 23 ) {
            return 'Dinner';
            
        }else {
            return'Late_night';
            
        }
    }

 

    public function getDisplayTitle(string $mealType):string
    {
       return match ($mealType){
        'Breakfast'=>  'Morning BreakFast ',
        'Lunch' => 'Lunch Delights',
        'Snack'=> 'TeaTime Snacks',
        'Dinner' => 'Evening Delights',
        default => 'Late night cravings',
       };
    }
}