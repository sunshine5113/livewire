<?php

namespace Mediconesystems\LivewireDatatables;

class NumberColumn extends Column
{
    public $type = 'number';
    public $align = 'right';
    public $round;

    public function round($places = 0)
    {
        $this->round = $places;

        $this->callback = function ($value) {
            return round($value, $this->round);
        };

        return $this;
    }
    
    public function format($places = 0)
    {
        $this->callback = function ($value) use ($places) {
            return number_format($value, $places, '.', ',');
        };

        return $this;
    }
}
