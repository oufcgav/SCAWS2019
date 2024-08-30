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
                'Peter Kioso (D)',
                'Ciaron Brown (D)',
                'Jack Currie (D)',
                'Greg Leigh (D)',
                'Joe Bennett (D)',
                'Hidde ter Avest (D)',
                'Other (D)',
                'Idris El Mizouni (M)',
                'Josh McEachran (M)',
                'Cameron Brannagan (M)',
                'Louie Sibley (M)',
                'Will Vaulks (M)',
                'Max Woltman (M)',
                'Tyler Goodrham (M)',
                'Matt Phillips (M)',
                'Other (M)',
                'Mark Harris (S)',
                'Will Goodwin (S)',
                'Ruben Rodrigues (S)',
                'Siriki Dembele (S)',
                'Owen Dale (S)',
                'Malcolm Ebiowei (S)',
                'Dane Scarlett (S)',
                'Przemyslaw Placheta (S)',
                'Marselino Ferdinan (S)',
                'Other (S)',
                'Own goal (O)',
            ];
    }
}
