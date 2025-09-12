<?php

namespace App\Repository;

class SquadList
{
    public function getCurrentSquad(): array
    {
        return
            [
                'Jamie Cumming (GK)',
                'Matt Ingram (GK)',
                'Jacob Knightbridge (GK)',
                'Sam Long (D)',
                'Brodie Spencer (D)',
                'Michal Helik (D)',
                'Ciaron Brown (D)',
                'Greg Leigh (D)',
                'Hidde ter Avest (D)',
                'Jack Currie (D)',
                'Ben Davies (D)',
                'Other (D)',
                'Cameron Brannagan (M)',
                'Louie Sibley (M)',
                'Will Vaulks (M)',
                'Matt Phillips (M)',
                'Brian De Keersmaecker (M)',
                'Przemyslaw Placheta (M)',
                'Luke Harris (M)',
                'Stan Mills (M)',
                'Filip Krastev (M)',
                'Other (M)',
                'Mark Harris (S)',
                'Ole Romeny (S)',
                'Siriki Dembele (S)',
                'Tom Bradshaw (S)',
                'Tyler Goodrham (S)',
                'Nik Prelec (S)',
                'William Lankshear (S)',
                'Gatlin ODonkor (S)',
                'Other (S)',
                'Own goal (O)',
            ];
    }
}
