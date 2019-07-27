<?php

namespace Sanjab\Models;

use Illuminate\Database\Eloquent\Model;

class TempModel extends Model
{
    public function setCasts(array $casts)
    {
        foreach ($casts as $cast) {
            $this->casts[$cast] = 'array';
        }
    }
}
