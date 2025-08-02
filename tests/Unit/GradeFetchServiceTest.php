<?php

namespace Tests\Unit;

use App\Modules\Grade\Models\Grade;
use App\Modules\Grade\Services\GradeFetchService;
use App\Modules\Term\Models\Term;
use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\Student\Models\StudentSession;
use App\Modules\Assignement\Models\Assignement;
use Mockery;
use Tests\TestCase;

class GradeFetchServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_returns_error_if_no_current_academic_year()
    {
        AcademicYear::shouldReceive('getCurrentAcademicYear')->once()->andReturn(null);
        $result = GradeFetchService::fetchStudentGrades(1, 1);
        $this->assertEquals(['error' => 'No current academic year found.'], $result);
    }

    /** @test */
    public function it_returns_error_if_no_current_term()
    {
        AcademicYear::shouldReceive('getCurrentAcademicYear')->once()->andReturn((object)['id' => 1]);
        Term::shouldReceive('getCurrentTerm')->once()->andReturn(null);
        $result = GradeFetchService::fetchStudentGrades(1, 1);
        $this->assertEquals(['error' => 'No current term found.'], $result);
    }
}
