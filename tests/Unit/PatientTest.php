<?php

namespace Tests\Unit;

use App\ValueObject\Patient;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PatientTest extends TestCase
{
    use LazilyRefreshDatabase;

    /** @test */
    public function it_check_a_user_is_patient()
    {
        $user = $this->user(['is_patient' => true]);
        $user2 = $this->user(['is_patient' => false]);

        $this->assertTrue($user->isPatient());
        $this->assertFalse($user2->isPatient());
    }

    /** @test */
    public function it_has_patient_value_object()
    {
        $user = $this->user([
            'is_patient' => true,
            'name' => 'andi',
        ]);

        $patient = Patient::init($user);
        $this->assertEquals('andi',$patient->name);
        $this->assertTrue($patient->isPatient());
    }

    /** @test */
    public function it_throws_exception_if_user_is_not_a_patient_type()
    {
        $this->expectException(\InvalidArgumentException::class);
        $user = $this->user(['is_patient' => false]);

        Patient::init($user);
    }
}
