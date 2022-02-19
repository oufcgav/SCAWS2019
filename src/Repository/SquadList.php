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
                'Elliott Moore (D)',
                'Sam Long (D)',
                'John Mousinho (D)',
                'Steve Seddon (D)',
                'Ciaron Brown (D)',
                'Michael Elechi (D)',
                'Luke McNally (D)',
                'Other (D)',
                'Alejandro Gorrin (M)',
                'Cameron Brannagan (M)',
                'Mark Sykes (M)',
                'Anthony Forde (M)',
                'Joel Cooper (M)',
                'Marcus McGuane (M)',
                'Jamie Hanson (M)',
                'Ryan Williams (M)',
                'Herbie Kane (M)',
                'Oisin Smyth (M)',
                'Other (M)',
                'Gavin Whyte (S)',
                'Matty Taylor (S)',
                'Sam Winnall (S)',
                'James Henry (S)',
                'Sam Baldock (S)',
                'Billy Bodin (S)',
                'Nathan Holland (S)',
                'Derick Osei Yaw (S)',
                'Dan! (S)',
                'Other (S)',
                'Own goal (O)',
            ];
    }
}
