<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Appointment;
use App\Slot;
use App\Schedule;
use App\WorkstationService;
use App\Models\BranchScheduleTemplateDetail;
use App\Services\AppointmentService;

use Illuminate\Support\Carbon;

class AppointmentServiceTest extends TestCase
{
    use RefreshDatabase;

    private $appointmentService, $slot;

    public function setUp(): void
    {
        parent::setUp();

        $this->setupOrganization('free', 'appointment');

        $this->slot = Slot::factory()->state([
            'service_id' => $this->service->id,
            'max_slots' => 2,
            'start_time' => Carbon::now()->subHours(3)->format('H:i'),
            'end_time' => Carbon::now()->subMinutes(1)->format('H:i')
        ])->create();

        $this->appointmentService = app(AppointmentService::class);
    }

    public function test_free_appointment_exceeded()
    {
        $date = date('Y-m-d');

        Appointment::factory()
            ->count(5)
            ->state([
                'branch_id' => $this->branch->id,
                'service_id' => $this->service->id,
                'date' => date('Y-m-d'),
                'slot_id' => $this->slot->id
            ])
            ->create();

        $result = $this->appointmentService->isFreeAppointmentExceeded($this->branch->id, $date);

        $this->assertTrue($result);
    }

    public function test_duplicate_appointments()
    {
        $date = date('Y-m-d');
        $email = 'abdis@mail.com';
        $phone = '0851569776458';

        Appointment::factory()->create([
            'email' => $email,
            'phone' => $phone,
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'date' => $date,
            'slot_id' => $this->slot->id
        ]);

        $result = $this->appointmentService->isAppoinmentDuplicate([
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'date' => $date,
            'email' => $email,
            'phone' => $phone,      
            'slot_id' => $this->slot->id
        ]);

        $this->assertTrue($result);
    }

    public function test_appointment_on_full_slot()
    {
        $date = date('Y-m-d');

        Appointment::factory()
            ->count(2)
            ->state([
                'branch_id' => $this->branch->id,
                'service_id' => $this->service->id,
                'date' => $date,
                'slot_id' => $this->slot->id
            ])
            ->create();
        
        $result = $this->appointmentService->isAppointmentSlotFull($this->slot->id, $date);

        $this->assertTrue($result);
    }

    public function test_appointment_on_holiday()
    {
        $holiday = BranchScheduleTemplateDetail::factory()
            ->state(['branch_id' => $this->branch->id])
            ->create();

        $result = $this->appointmentService->isHoliday($this->branch->id, $holiday->date);

        $this->assertTrue($result);
    }

    public function test_appointment_on_closed_day()
    {
        $closedSchedule = Schedule::factory()
            ->state([
                'branch_id' => $this->branch->id,
                'status' => 'closed'
            ])
            ->create();

        $date = date('Y-m-d', strtotime($closedSchedule->day . ' september 2022'));
        $result = $this->appointmentService->isClosed($this->branch->id, $date);

        $this->assertTrue($result);
    }

    public function test_finished_session_appointment()
    {
        $result = $this->appointmentService->isAppointmentSessionFinish($this->slot->id, date('Y-m-d'));

        $this->assertTrue($result);
    }

    public function test_persist_appointment_to_database()
    {
        $slot = Slot::factory()
            ->state([
                'service_id' => $this->service->id,
                'start_time' => date('H:i'),
                'end_time' =>  date('H:i', strtotime('+3 hours'))
            ])
            ->create();
        
        WorkstationService::factory()
            ->state([
                'service_id' => $this->service->id,
                'workstation_id' => $this->workstation->id
            ])
            ->create();

        $this->appointmentService->create([
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'date' => date('Y-m-d'),
            'slot_id' => $slot->id,
            'name' => 'Abdis',
            'email' => 'abdis@mail.com',
            'phone' => '085156976458'
        ]);

        $this->assertDatabaseCount('appointments', 1);
    }
}
