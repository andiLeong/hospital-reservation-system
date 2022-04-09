<?php

namespace App\ValueObject;


use App\Exceptions\ReservationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class TimeFrame
{
    private $supportedTimeFrames = [
        'am' => [
            '08:00-09:00',
            '09:00-10:00',
            '10:00-11:00',
        ],
        'pm' => [
            '14:00-15:00',
            '15:00-16:00',
            '16:00-17:00',
        ],
    ];

    private $timeFrame;

    private function __construct($timeFrame)
    {
        $this->timeFrame = $this->validate($timeFrame);
    }

    public static function make($timeFrame)
    {
        return new static($timeFrame);
    }

    public function validate($timeFrame)
    {
        if(!$this->times()->contains($timeFrame)){
            throw new ReservationException('Time frame isn\'t supported');
        }
        return $timeFrame;
    }

    public function times() :Collection
    {
        return $this->supportedTimeFrames()->flatten(1);
    }

    public function getShift()
    {
        ['pm' => $pm] = $this->supportedTimeFrames();
        if(array_key_exists($this->timeFrame,array_flip($pm))){
            return 'pm';
        }
        return 'am';
    }

    public static function supportedTimeFrames() :Collection
    {
        return collect(Config::get('times'));
    }

    public function __toString(): string
    {
        return $this->timeFrame;
    }
}
