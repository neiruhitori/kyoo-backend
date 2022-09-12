<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Appointment;
use App\Services\AppointmentService;
use App\Slot;
use Throwable;
use App\Models\BranchScheduleTemplateDetail;
use App\Schedule;
use Illuminate\Support\Carbon;

class AppointmentServiceTest extends TestCase
{
    use RefreshDatabase;

    private $appointmentService;

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
        Appointment::factory()
            ->count(5)
            ->state([
                'branch_id' => $this->branch->id,
                'service_id' => $this->service->id,
                'date' => date('Y-m-d'),
                'slot_id' => $this->slot->id
            ])
            ->create();

        $message = 'Appointment gratis tersedia';

        try {
            $this->appointmentService->create([
                'branch_id' => $this->branch->id,
                'date' => date('Y-m-d'),
                'slot_id' => $this->slot->id
            ]);
        } catch (Throwable $e) {
            $message = $e->getMessage();
        }

        $this->assertEquals($message, 'Batas appointment gratis hari ini terlampaui');
    }

    public function test_duplicate_appointments()
    {
        $email = 'abdis@mail.com';
        $phone = '0851569776458';

        Appointment::factory()->create([
            'email' => $email,
            'phone' => $phone,
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'date' => date('Y-m-d'),
            'slot_id' => $this->slot->id
        ]);

        $message = 'Appointment belum terdaftar';

        try {
            $this->appointmentService->create([
                'branch_id' => $this->branch->id,
                'date' => date('Y-m-d'),
                'email' => $email,
                'phone' => $phone,      
                'slot_id' => $this->slot->id
            ]);
        } catch(Throwable $e) {
            $message = $e->getMessage();
        }

        $this->assertEquals($message, 'Appointment telah terdaftar');
    }

    public function test_appointment_exceed_max_slot()
    {
        Appointment::factory()
            ->count(2)
            ->state([
                'branch_id' => $this->branch->id,
                'service_id' => $this->service->id,
                'date' => date('Y-m-d'),
                'slot_id' => $this->slot->id
            ])
            ->create();
        
        $message = 'Sesi appointment tersedia';

        try {
            $this->appointmentService->create([
                'branch_id' => $this->branch->id,
                'service_id' => $this->service->id,
                'date' => date('Y-m-d'),
                'slot_id' => $this->slot->id
            ]);
        } catch (Throwable $e) {
            $message = $e->getMessage();
        }

        $this->assertEquals($message, 'Sesi appointment tidak tersedia');
    }

    public function test_appointment_on_holiday()
    {
        $holiday = BranchScheduleTemplateDetail::factory()
            ->state(['branch_id' => $this->branch->id])
            ->create();

        $message = 'Sesi appointment tersedia';

        try {
            $this->appointmentService->create([
                'branch_id' => $this->branch->id,
                'date' => $holiday->date,
                'slot_id' => $this->slot->id
            ]);
        } catch(Throwable $e) {
            $message = $e->getMessage();
        }

        $this->assertEquals($message, 'Sesi appointment tidak tersedia');
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

        $message = 'Sesi appointment tersedia';

        try {
            $this->appointmentService->create([
                'branch_id' => $this->branch->id,
                'date' => $date,
                'slot_id' => $this->slot->id
            ]);
        } catch(Throwable $e) {
            $message = $e->getMessage();
        }

        $this->assertEquals($message, 'Sesi appointment tidak tersedia');
    }

    public function test_past_schedule_appointment()
    {
        $message = 'Sesi appointment tersedia';

        try {
            $this->appointmentService->create([
                'branch_id' => $this->branch->id,
                'date' => date('Y-m-d'),
                'slot_id' => $this->slot->id
            ]);
        } catch(Throwable $e) {
            $message = $e->getMessage();
        }

        $this->assertEquals($message, 'Sesi appointment sudah berakhir');
    }

    public function test_store_appointment()
    {
        $slot = Slot::factory()
            ->state([
                'service_id' => $this->service->id,
                'start_time' => date('H:i'),
                'end_time' =>  date('H:i', strtotime('+3 hours'))
            ])
            ->create();

        $this->appointmentService->create([
            'branch_id' => $this->branch->id,
            'date' => date('Y-m-d'),
            'slot_id' => $slot->id,
            'name' => 'Abdis',
            'email' => 'abdis@mail.com',
            'phone' => '085156976458'
        ]);

        $this->assertDatabaseCount('appointments', 1);
    }
}
