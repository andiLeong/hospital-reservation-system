<?php

namespace App\Collections;


class TimesCollection
{

    private $timesFrames;

    public function __construct($timeFrames)
    {
        $this->timesFrames = $timeFrames;
    }

    public function get(string $time = null)
    {
        if(is_null($time)){
            return $this->format();
        }

       $this->TimeExist($time);

        return $this->format()->filter(fn($timeFrame,$shiftTime) => $shiftTime == $time);
    }

    private function format()
    {
        return $this->timesFrames->map(function($timeFrames){
            return collect($timeFrames)->map(fn($timeFrame) => ['time' => $timeFrame]);
        });
    }

    private function TimeExist($time)
    {
        $shifts = $this->timesFrames->keys();
        if($shifts->doesntContain($time)){
            throw new \InvalidArgumentException('time not supported');
        }
    }
}
