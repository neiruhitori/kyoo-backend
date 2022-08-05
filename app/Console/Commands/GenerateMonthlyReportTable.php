<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class GenerateMonthlyReportTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To generate monthly report table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tableDate = date('Ym');

        $departmentTable = 'department_general_report_' . $tableDate;
        $serviceTable = 'service_general_report_' . $tableDate;
        $serviceDistributionTable = 'service_distribution_general_report_' . $tableDate;
        $workstationTable = 'workstation_general_report_' . $tableDate;
        $vctTable = 'vct_general_report_' . $tableDate;

        if (!Schema::hasTable($departmentTable)) {
            Schema::create($departmentTable, function (Blueprint $table) {
                $table->id();
                $table->date('event_date');
                $table->bigInteger('branch_id');
                $table->bigInteger('department_id');
                $table->string('name');
                $table->bigInteger('total_queue');
                $table->bigInteger('total_served');
                $table->bigInteger('total_no_show');
                $table->bigInteger('shortest_wait_duration');
                $table->bigInteger('average_wait_duration');
                $table->bigInteger('longest_wait_duration');
                $table->bigInteger('shortest_serve_duration');
                $table->bigInteger('average_serve_duration');
                $table->bigInteger('longest_serve_duration');
                $table->timestamps();

                $table->foreign('branch_id')->references('id')->on('branches');
                $table->foreign('department_id')->references('id')->on('departments');
            });
        }

        if (!Schema::hasTable($serviceTable)) {
            Schema::create($serviceTable, function (Blueprint $table) {
                $table->id();
                $table->date('event_date');
                $table->bigInteger('branch_id');
                $table->bigInteger('department_id');
                $table->bigInteger('service_id');
                $table->string('name');
                $table->bigInteger('total_queue');
                $table->bigInteger('total_served');
                $table->bigInteger('total_no_show');
                $table->bigInteger('shortest_wait_duration');
                $table->bigInteger('average_wait_duration');
                $table->bigInteger('longest_wait_duration');
                $table->bigInteger('shortest_serve_duration');
                $table->bigInteger('average_serve_duration');
                $table->bigInteger('longest_serve_duration');
                $table->timestamps();

                $table->foreign('branch_id')->references('id')->on('branches');
                $table->foreign('department_id')->references('id')->on('departments');
                $table->foreign('service_id')->references('id')->on('services');
            });
        }

        if (!Schema::hasTable($serviceDistributionTable)) {
            Schema::create($serviceDistributionTable, function (Blueprint $table) {
                $table->id();
                $table->date('event_date');
                $table->bigInteger('branch_id');
                $table->bigInteger('department_id');
                $table->bigInteger('service_id');
                $table->string('name');
                $table->integer('_0_5');
                $table->integer('_5_10');
                $table->integer('_10_15');
                $table->integer('_15_20');
                $table->integer('_20_25');
                $table->integer('_25_30');
                $table->integer('_30_');
                $table->timestamps();

                $table->foreign('branch_id')->references('id')->on('branches');
                $table->foreign('department_id')->references('id')->on('departments');
                $table->foreign('service_id')->references('id')->on('services');
            });
        }

        if (!Schema::hasTable($workstationTable)) {
            Schema::create($workstationTable, function (Blueprint $table) {
                $table->id();
                $table->date('event_date');
                $table->bigInteger('branch_id');
                $table->bigInteger('department_id');
                $table->bigInteger('workstation_id');
                $table->string('name');
                $table->bigInteger('total_queue');
                $table->bigInteger('total_served');
                $table->bigInteger('total_no_show');
                $table->bigInteger('shortest_wait_duration');
                $table->bigInteger('average_wait_duration');
                $table->bigInteger('longest_wait_duration');
                $table->bigInteger('shortest_serve_duration');
                $table->bigInteger('average_serve_duration');
                $table->bigInteger('longest_serve_duration');
                $table->bigInteger('total_operating_duration');
                $table->bigInteger('total_serve_duration');
                $table->timestamps();

                $table->foreign('branch_id')->references('id')->on('branches');
                $table->foreign('department_id')->references('id')->on('departments');
                $table->foreign('workstation_id')->references('id')->on('workstations');
            });
        }

        if (!Schema::hasTable($vctTable)) {
            Schema::create($vctTable, function (Blueprint $table) {
                $table->id();
                $table->date('event_date');
                $table->bigInteger('branch_id');
                $table->bigInteger('department_id');
                $table->bigInteger('vct_id');
                $table->string('name');
                $table->bigInteger('total_queue');
                $table->bigInteger('total_served');
                $table->bigInteger('total_no_show');
                $table->bigInteger('shortest_wait_duration');
                $table->bigInteger('average_wait_duration');
                $table->bigInteger('longest_wait_duration');
                $table->bigInteger('shortest_serve_duration');
                $table->bigInteger('average_serve_duration');
                $table->bigInteger('longest_serve_duration');
                $table->bigInteger('total_operating_duration');
                $table->bigInteger('total_serve_duration');
                $table->timestamps();

                $table->foreign('branch_id')->references('id')->on('branches');
                $table->foreign('department_id')->references('id')->on('departments');
                $table->foreign('vct_id')->references('id')->on('users');
            });
        }
    }
}
