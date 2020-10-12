<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static Positions GOALKEEPER()
 * @method static Positions DEFENDERS()
 * @method static Positions MIDFIELDERS()
 * @method static Positions STRIKERS()
 */
class Positions extends Enum
{
    private const GOALKEEPER = 'Goalkeeper';
    private const DEFENDERS = 'Defenders';
    private const MIDFIELDERS = 'Midfielders';
    private const STRIKERS = 'Strikers';

}