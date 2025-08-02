<?php

namespace Tests\Unit;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\AcademicYear\Services\AcademicYearService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

class AcademicYearServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_returns_all_academic_years()
    {
        $years = new Collection([
            (object)['id' => 1, 'name' => '2023-2024'],
            (object)['id' => 2, 'name' => '2024-2025'],
        ]);
        AcademicYear::shouldReceive('all')->once()->andReturn($years);
        $service = new AcademicYearService();
        $result = $service->getAllAcademicYears();
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals('2023-2024', $result[0]->name);
    }

    /** @test */
    public function it_returns_academic_year_by_id()
    {
        $year = (object)['id' => 1, 'name' => '2023-2024'];
        AcademicYear::shouldReceive('find')->with(1)->once()->andReturn($year);
        $service = new AcademicYearService();
        $result = $service->getAcademicYearById(1);
        $this->assertEquals($year, $result);
    }
}

