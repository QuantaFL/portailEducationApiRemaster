<?php

namespace Tests\Unit;

use App\Modules\Teacher\Models\Teacher;
use App\Modules\Teacher\Services\TeacherContractService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade as Pdf;
use PHPUnit\Framework\TestCase;
use Mockery;

class TeacherContractServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_logs_and_sends_contract_email()
    {
        $teacher = Mockery::mock(Teacher::class);

        Log::shouldReceive('info')->once();
        Pdf::shouldReceive('loadView')->once()->andReturnSelf();
        Pdf::shouldReceive('download')->once()->andReturn('pdf-content');
        Mail::shouldReceive('to')->once()->andReturnSelf();
        Mail::shouldReceive('send')->once();

        $service = new TeacherContractService();
        $result = $service->generateAndSendContract($teacher);

        $this->assertTrue($result);
    }
}

