<?php

namespace App\Model;

class RatingDTO
{
    public int $score;       // 1 a 5
    public ?string $comment = null;
}
