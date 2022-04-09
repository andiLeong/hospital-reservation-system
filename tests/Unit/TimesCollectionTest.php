<?php

namespace Tests\Unit;

use App\Collections\TimesCollection;
use Tests\TestCase;


class TimesCollectionTest extends TestCase
{


    private $collection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->collection = resolve(TimesCollection::class);
    }

    /** @test */
    public function it_throw_exception_if_time_not_found_from_the_collection()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->collection->get('aaam');
    }

    /** @test */
    public function it_can_get_a_list_timeframes_for_both_times()
    {
        $collection = $this->collection->get();

        $this->assertArrayHasKey('am', $collection->toArray());
        $this->assertArrayHasKey('pm', $collection->toArray());
    }

    /** @test */
    public function it_can_get_a_pm_timeframes()
    {
        $collection = $this->collection->get('pm');

        $this->assertEquals([
            ['time' => '14:00-15:00'],
            ['time' => '15:00-16:00'],
            ['time' => '16:00-17:00'],
        ], $collection->first()->toArray());
    }

    /** @test */
    public function it_can_get_a_am_timeframes()
    {
        $collection = $this->collection->get('am');

        $this->assertEquals([
            ['time' => '08:00-09:00'],
            ['time' => '09:00-10:00'],
            ['time' => '10:00-11:00'],
        ], $collection->first()->toArray());
    }
}
