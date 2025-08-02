<?php

namespace Tests\Unit;

use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\StudentSession;
use App\Modules\Student\Services\StudentInscriptionService;
use App\Modules\User\Models\UserModel;
use App\Modules\Parent\Models\ParentModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class StudentInscriptionServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_processes_inscription_and_creates_parent_and_student()
    {
        // Arrange
        $data = [
            'parent_email' => 'parent@example.com',
            'parent_first_name' => 'Parent',
            'parent_last_name' => 'Test',
            'student_first_name' => 'Student',
            'student_last_name' => 'Test',
            'student_birth_date' => '2010-01-01',
            // ... add other required fields ...
        ];

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never();

        $service = Mockery::mock(StudentInscriptionService::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $service->shouldAllowMockingProtectedMethods();
        $service->shouldReceive('handleParentCreation')->andReturn([
            'user' => Mockery::mock(UserModel::class),
            'parent_model' => Mockery::mock(ParentModel::class),
            'just_created' => true,
            'password' => 'secret123',
        ]);
        $service->shouldReceive('handleStudentCreation')->andReturn([
            'student' => Mockery::mock(Student::class),
            'student_session' => Mockery::mock(StudentSession::class),
        ]);
        $service->shouldReceive('sendWelcomeMail')->andReturn(true);

        // Act
        $result = $service->processInscription($data);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('student', $result);
        $this->assertArrayHasKey('student_session', $result);
        $this->assertArrayHasKey('parent', $result);
        $this->assertArrayHasKey('parent_user', $result);
        $this->assertArrayHasKey('parent_just_created', $result);
        $this->assertArrayHasKey('parent_password', $result);
    }
}
