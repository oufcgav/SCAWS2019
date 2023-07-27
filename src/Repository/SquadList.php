<?php

namespace App\Repository;

class SquadList
{
    public function getCurrentSquad(): array
    {
        return
            [
                'Simon Eastwood (GK)',
                'Ed McGinty (GK)',
                'James Beadle (GK)',
                'Elliott Moore (D)',
                'Sam Long (D)',
                'Jordan Thorniley (D)',
                'Finley Stevens (D)',
                'Ciaron Brown (D)',
                'James Golding (D)',
                'Stephan Negru (D)',
                'Teddy Mfuni (D)',
                'Other (D)',
                'Josh McEachran (M)',
                'Cameron Brannagan (M)',
                'Josh Murphy (M)',
                'Marcus McGuane (M)',
                'James Henry (M)',
                'Oisin Smyth (M)',
                'Josh Johnson (M)',
                'Tyler Goodrham (M)',
                'Other (M)',
                'Yanic Wildschut (S)',
                'Mark Harris (S)',
                'Ruben Rodrigues (S)',
                'Marcus Browne (S)',
                'Stan Mills (S)',
                'Billy Bodin (S)',
                'Gatlin ODonkor (S)',
                'Dan! (S)',
                'Other (S)',
                'Own goal (O)',
            ];
    }
}
