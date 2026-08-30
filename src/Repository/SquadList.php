<?php

namespace App\Repository;

class SquadList
{
    public function getCurrentSquad(): array
    {
        return
            [
                'Jamie Cumming (GK)',
                'Simon Eastwood (GK)',
                'Sam Long (D)',
                'Brodie Spencer (D)',
                'Ruben Roosken (D)',
                'Michal Helik (D)',
                'Ciaron Brown (D)',
                'Greg Leigh (D)',
                'Jack Currie (D)',
                'Peter Kioso (D)',
                'James Golding (D)',
                'Other (D)',
                'Cameron Brannagan (M)',
                'Jamie McDonnell (M)',
                'Tyler Goodrham (M)',
                'Will Vaulks (M)',
                'Brian De Keersmaecker (M)',
                'Louis Sibley (M)',
                'Kai Andrews (M)',
                'Other (M)',
                'Mark Harris (S)',
                'Aidomo Emakhu (S)',
                'Siriki Dembele (S)',
                'Stan Mills (S)',
                'Jin Woo Jeon (S)',
                'Gatlin ODonker (S)',
                'Other (S)',
                'Own goal (O)',
            ];
    }
}
