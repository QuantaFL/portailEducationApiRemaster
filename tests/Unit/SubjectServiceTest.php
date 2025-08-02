<?php

namespace Tests\Unit;

use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Services\SubjectService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Mockery;
use PHPUnit\Framework\TestCase;

class SubjectServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_returns_all_subjects()
    {
        $subjects = new Collection([
            (object)['id' => 1, 'name' => 'Math'],
            (object)['id' => 2, 'name' => 'Science'],
        ]);

        Log::shouldReceive('info')->twice();
        Subject::shouldReceive('all')->once()->andReturn($subjects);

        $service = new SubjectService();
        $result = $service->getAllSubjects();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals('Math', $result[0]->name);
    }
}

