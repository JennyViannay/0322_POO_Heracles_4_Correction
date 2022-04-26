<?php

namespace App;

use Exception;

class Arena 
{
    private array $monsters;
    private Hero $hero;

    // directions possibles
    public const DIRECTIONS = [
        'N' => [0, -1],
        'S' => [0, 1],
        'E' => [1, 0],
        'W' => [-1, 0],
    ];

    private int $size = 10;

    public function __construct(Hero $hero, array $monsters)
    {
        $this->hero = $hero;
        $this->monsters = $monsters;
    }

    // Méthode move
    public function move(Fighter $fighter, string $direction)
    {
        // récupérer les coordonnées du fighter
        $x = $fighter->getX();
        $y = $fighter->getY();
        // s'assurer que le fighter va dans une direction valide
        if (!key_exists($direction, self::DIRECTIONS)) {
            throw new Exception('Unknown direction');
        }

        // récupérer les coordonnées de la position future
        $destinationX = $x + self::DIRECTIONS[$direction][0];
        $destinationY = $y + self::DIRECTIONS[$direction][1];

        // s'assurer que la position future du fighter ne sorte pas de l'arene
        if ($destinationX < 0 || $destinationX >= $this->getSize() || $destinationY < 0 || $destinationY >= $this->getSize()) {
            throw new Exception('Out of Map');
        }

        // s'assurer que la position n'est pas déjà prise par un des monstres
        foreach ($this->getMonsters() as $monster) {
            if ($monster->getX() == $destinationX && $monster->getY() == $destinationY) {
                throw new Exception('Not free');
            }
        }

        // si tout est ok on déplace le fighter
        $fighter->setX($destinationX);
        $fighter->setY($destinationY);
    }

    // méthode battle
    public function battle(int $id): void
    {
        // récupérer le monstre à combattre
        $monster = $this->getMonsters()[$id];

        // vérifier que le monstre est atteignable
        if ($this->touchable($this->getHero(), $monster)) {
            // si oui on lance la bataille
            $this->getHero()->fight($monster);
        } else {
            // si non on lève une d'erreur
            throw new Exception('Monster out of range');
        }

        // vérifier si le monstre est mort
        if (!$monster->isAlive()) {
            // si oui on le supprime de la liste des monstres et on ajoute les points d'exp au hero
            $this->getHero()->setExperience($this->getHero()->getExperience() + $monster->getExperience());
            unset($this->monsters[$id]);
        } else {
            // sinon le monstre attaque le héro si celui ci est atteignable
            if ($this->touchable($monster, $this->getHero())) {
                $monster->fight($this->getHero());
            } else {
                throw new Exception('Hero out of range');
            }
        }
    }


    public function getDistance(Fighter $startFighter, Fighter $endFighter): float
    {
        $Xdistance = $endFighter->getX() - $startFighter->getX();
        $Ydistance = $endFighter->getY() - $startFighter->getY();
        return sqrt($Xdistance ** 2 + $Ydistance ** 2);
    }

    public function touchable(Fighter $attacker, Fighter $defenser): bool 
    {
        return $this->getDistance($attacker, $defenser) <= $attacker->getRange();
    }

    /**
     * Get the value of monsters
     */ 
    public function getMonsters(): array
    {
        return $this->monsters;
    }

    /**
     * Set the value of monsters
     *
     */ 
    public function setMonsters($monsters): void
    {
        $this->monsters = $monsters;
    }

    /**
     * Get the value of hero
     */ 
    public function getHero(): Hero
    {
        return $this->hero;
    }

    /**
     * Set the value of hero
     */ 
    public function setHero($hero): void
    {
        $this->hero = $hero;
    }

    /**
     * Get the value of size
     */ 
    public function getSize(): int
    {
        return $this->size;
    }
}