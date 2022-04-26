<?php

namespace App;

// nouvelle class level
class Level
{

    // methode statique calculate
    static public function calculate(int $experience): int 
    {
        return ceil($experience / 1000);
    }
} 