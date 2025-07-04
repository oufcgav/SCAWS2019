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
                'Simon Eastwood (GK)',
                'Elliott Moore (D)',
                'Sam Long (D)',
                'Jordan Thorniley (D)',
                'Michal Helik (D)',
                'Peter Kioso (D)',
                'Ciaron Brown (D)',
                'Greg Leigh (D)',
                'Hidde ter Avest (D)',
                'Other (D)',
                'Cameron Brannagan (M)',
                'Louie Sibley (M)',
                'Will Vaulks (M)',
                'Tyler Goodrham (M)',
                'Matt Phillips (M)',
                'Brian De Keersmaecker (M)',
                'Other (M)',
                'Mark Harris (S)',
                'Ole Romeny (S)',
                'Siriki Dembele (S)',
                'Tom Bradshaw (S)',
                'Przemyslaw Placheta (S)',
                'Marselino Ferdinan (S)',
                'Other (S)',
                'Own goal (O)',
            ];
    }
}
