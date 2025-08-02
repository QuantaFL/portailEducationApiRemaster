<?php

namespace Tests\Unit;

use App\Modules\Assignement\Models\Assignement;
use App\Modules\Assignement\Services\AssignmentService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

class AssignmentServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_returns_all_assignments()
    {
        $assignments = new Collection([
            (object)['id' => 1, 'title' => 'Assignment 1'],
            (object)['id' => 2, 'title' => 'Assignment 2'],
        ]);
        Assignement::shouldReceive('all')->once()->andReturn($assignments);
        $service = new AssignmentService();
        $result = $service->getAllAssignments();
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals('Assignment 1', $result[0]->title);
    }

    /** @test */
    public function it_returns_assignment_by_id()
    {
        $assignment = (object)['id' => 1, 'title' => 'Assignment 1'];
        Assignement::shouldReceive('find')->with(1)->once()->andReturn($assignment);
        $service = new AssignmentService();
        $result = $service->getAssignmentById(1);
        $this->assertEquals($assignment, $result);
    }
}

