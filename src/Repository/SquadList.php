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
                'Ruben Roosken (D)',
                'Christ Makosso (D)',
                'Other (D)',
                'Cameron Brannagan (M)',
                'Jamie Donley (M)',
                'Yunus Konak (M)',
                'Jamie McDonnell (M)',
                'Will Vaulks (M)',
                'Brian De Keersmaecker (M)',
                'Przemyslaw Placheta (M)',
                'Miles Peart-Harris (M)',
                'Stan Mills (M)',
                'Other (M)',
                'Mark Harris (S)',
                'Ole Romeny (S)',
                'Aidomo Emakhu (S)',
                'Siriki Dembele (S)',
                'Tyler Goodrham (S)',
                'Nik Prelec (S)',
                'William Lankshear (S)',
                'Jin Woo Jeon (S)',
                'Other (S)',
                'Own goal (O)',
            ];
    }
}
