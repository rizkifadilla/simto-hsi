<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Applicant;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApplicantRelationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function applicant_has_many_applications()
    {
        $applicant = Applicant::create([
            'name' => 'Test',
            'email' => 'test@mail.com',
            'cv_file' => 'cv.pdf'
        ]);

        $relation = $applicant->applications();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $relation
        );
    }
}