<?php

namespace Tests\Unit;

use App\Modules\Grade\Services\GradeFetchService;
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
        $mockAcademicYear = Mockery::mock('alias:App\Modules\AcademicYear\Models\AcademicYear');
        $mockAcademicYear->shouldReceive('getCurrentAcademicYear')->once()->andReturn(null);
        $result = GradeFetchService::fetchStudentGrades(1, 1);
        $this->assertEquals(['error' => 'No current academic year found.'], $result);
    }

    /** @test */
    public function it_returns_error_if_no_current_term()
    {
        $mockAcademicYear = Mockery::mock('alias:App\Modules\AcademicYear\Models\AcademicYear');
        $mockAcademicYear->shouldReceive('getCurrentAcademicYear')->once()->andReturn(new \App\Modules\AcademicYear\Models\AcademicYear());
        $mockTerm = Mockery::mock('alias:App\Modules\Term\Models\Term');
        $mockTerm->shouldReceive('getCurrentTerm')->once()->andReturn(null);
        $result = GradeFetchService::fetchStudentGrades(1, 1);
        $this->assertEquals(['error' => 'No current term found.'], $result);
    }
}
