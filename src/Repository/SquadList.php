<?php

namespace App\Repository;

class SquadList
{
    public function getCurrentSquad(): array
    {
        return
            [
                'Simon Eastwood (GK)',
                'Jack Stevens (GK)',
                'Ed McGinty (GK)',
                'Elliott Moore (D)',
                'Sam Long (D)',
                'John Mousinho (D)',
                'Steve Seddon (D)',
                'Ciaron Brown (D)',
                'Stuart Findlay (D)',
                'James Golding (D)',
                'Other (D)',
                'Alejandro Gorrin (M)',
                'Cameron Brannagan (M)',
                'Josh Murphy (M)',
                'Ben Davis (M)',
                'Marcus Browne (M)',
                'Marcus McGuane (M)',
                'James Henry (M)',
                'Oisin Smyth (M)',
                'Other (M)',
                'Yanic Wildschut (S)',
                'Matty Taylor (S)',
                'Sam Baldock (S)',
                'Billy Bodin (S)',
                'Slavi Spasov (S)',
                'Dan! (S)',
                'Other (S)',
                'Own goal (O)',
            ];
    }
}
