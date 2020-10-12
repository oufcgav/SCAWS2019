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
                'Joshua Ruffels (D)',
                'Elliott Moore (D)',
                'Sam Long (D)',
                'John Mousinho (D)',
                'Robert Atkinson (D)',
                'Nico Jones (D)',
                'Sean Clare (D)',
                'Other (D)',
                'Alejandro Gorrin (M)',
                'Cameron Brannagan (M)',
                'Anthony Forde  (M)',
                'Mark Sykes (M)',
                'Jamie Hanson (M)',
                'Malachi Napa (M)',
                'Joel Cooper (M)',
                'Marcus McGuane (M)',
                'Liam Kelly (M)',
                'Other (M)',
                'Sam Winnall (S)',
                'Rob Hall (S)',
                'James Henry (S)',
                'Slavi Spasov (S)',
                'Daniel Agyei (S)',
                'Matty Taylor (S)',
                'Dylan Asonganyi (S)',
                'Derick Osei Yaw (S)',
                'Dan! (S)',
                'Other (S)',
                'Own goal (O)',
            ];
    }
}
