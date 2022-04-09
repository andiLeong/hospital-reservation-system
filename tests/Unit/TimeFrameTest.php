<?php

namespace Tests\Unit;

use App\ValueObject\TimeFrame;
use PHPUnit\Framework\TestCase;

class TimeFrameTest extends TestCase
{
    /** @test */
    public function it_can_get_the_shift_type()
    {
        $am = TimeFrame::make('10:00-11:00')->getShift();
        $pm = TimeFrame::make('15:00-16:00')->getShift();
        $this->assertEquals('am',$am);
        $this->assertEquals('pm',$pm);
    }
}
