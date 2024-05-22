<?php

namespace App\Repository;

class SquadList
{
    public function getCurrentSquad(): array
    {
        return
            [
                'Simon Eastwood (GK)',
                'Elliott Moore (D)',
                'Sam Long (D)',
                'Jordan Thorniley (D)',
                'Finley Stevens (D)',
                'Ciaron Brown (D)',
                'Stephan Negru (D)',
                'Greg Leigh (D)',
                'Joe Bennett (D)',
                'Other (D)',
                'Josh McEachran (M)',
                'Cameron Brannagan (M)',
                'Josh Murphy (M)',
                'Marcus McGuane (M)',
                'James Henry (M)',
                'Oisin Smyth (M)',
                'Tyler Goodrham (M)',
                'Owen Dale (M)',
                'Other (M)',
                'Mark Harris (S)',
                'Will Goodwin (S)',
                'Ruben Rodrigues (S)',
                'Marcus Browne (S)',
                'Max Woltman (S)',
                'Other (S)',
                'Own goal (O)',
            ];
    }
}
