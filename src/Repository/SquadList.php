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
                'Brodie Spencer (D)',
                'Michal Helik (D)',
                'Peter Kioso (D)',
                'Ciaron Brown (D)',
                'Greg Leigh (D)',
                'Hidde ter Avest (D)',
                'Jack Currie (D)',
                'Other (D)',
                'Cameron Brannagan (M)',
                'Louie Sibley (M)',
                'Will Vaulks (M)',
                'Matt Phillips (M)',
                'Brian De Keersmaecker (M)',
                'Przemyslaw Placheta (M)',
                'Luke Harris (M)',
                'Stan Mills (M)',
                'Other (M)',
                'Mark Harris (S)',
                'Ole Romeny (S)',
                'Siriki Dembele (S)',
                'Tom Bradshaw (S)',
                'Tyler Goodrham (S)',
                'Nik Prelec (S)',
                'Marselino Ferdinan (S)',
                'William Lankshear (S)',
                'Other (S)',
                'Own goal (O)',
            ];
    }
}
