<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Department;
use App\DirectQueue;
use App\Models\CounterActivity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Service;
use App\Workstation;
use App\User;
use Illuminate\Support\Facades\Log;

class FeedReportTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:feed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Feed table using queue data summary daily';

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

        if (Schema::hasTable($departmentTable)) {
            $departments = Department::all();

            $data = $this->transformsDepartmentData($departments);

            DB::table($departmentTable)->insert($data->toArray());
        }

        if (Schema::hasTable($serviceTable)) {
            $services = Service::with('Department')->get();

            $data = $this->transformsServiceData($services);

            DB::table($serviceTable)->insert($data->toArray());
        }

        if (Schema::hasTable($serviceDistributionTable)) {
            $services = Service::with('Department')->get();

            $data = $this->transformsServiceDistributionData($services);

            DB::table($serviceDistributionTable)->insert($data->toArray());
        }

        if (Schema::hasTable($workstationTable)) {
            $workstations = Workstation::with('Department')->get();

            $data = $this->transformsWorkstationData($workstations);

            DB::table($workstationTable)->insert($data->toArray());
        }

        if (Schema::hasTable($vctTable)) {
            $users = User::whereHas('WorkstationVct')->get();

            $data = $this->transformsVctData($users);

            DB::table($vctTable)->insert($data->toArray());
        }
    }

    private function transformsDepartmentData($departments)
    {
        return $departments->filter(function ($department) {
            $id = $department->id;

            $total = DirectQueue::whereHas('Service', function ($query) use ($id) {
                $query->where('department_id', $id);
            })
                ->whereBetween('created_at', [
                    Carbon::yesterday()->startOfDay(),
                    Carbon::yesterday()->endOfDay()
                ])
                ->count();
            
            return $total > 0;
        })->map(function ($department) {
            $id = $department->id;
            
            $directQueue = DirectQueue::whereHas('Service', function ($query) use ($id) {
                $query->where('department_id', $id);
            })
                ->whereBetween('created_at', [
                    Carbon::yesterday()->startOfDay(),
                    Carbon::yesterday()->endOfDay()
                ]);
            $servedDirectQueue = $directQueue->clone()->whereIn('status', ['served', 'end served']);
            $endServedDirectQueue = $directQueue->clone()->where('status', 'end served');

            $total_queue = $directQueue->count();
            $total_served = $directQueue->clone()->whereIn('status', ['served', 'end served'])->count();
            $total_no_show = $directQueue->clone()->where('status', 'no show')->count();

            $shortest_wait_duration = $servedDirectQueue->min('waiting_duration');
            $average_wait_duration = floor($servedDirectQueue->avg('waiting_duration'));
            $longest_wait_duration = $servedDirectQueue->max('waiting_duration');
            
            $shortest_serve_duration = $endServedDirectQueue->min('serving_duration');
            $average_serve_duration = floor($endServedDirectQueue->avg('serving_duration'));
            $longest_serve_duration = $endServedDirectQueue->max('serving_duration');

            return [
                'event_date' => Carbon::yesterday()->format('Y-m-d'),
                'branch_id' => $department->branch_id,
                'department_id' => $id,
                'name' => $department->name,
                'total_queue' => $total_queue,
                'total_served' => $total_served,
                'total_no_show' => $total_no_show,
                'shortest_wait_duration' => $shortest_wait_duration,
                'average_wait_duration' => $average_wait_duration,
                'longest_wait_duration' => $longest_wait_duration,
                'shortest_serve_duration' => $shortest_serve_duration,
                'average_serve_duration' => $average_serve_duration,
                'longest_serve_duration' => $longest_serve_duration,
                'created_at' => date('Y-m-d H:i:s')
            ];
        });
    }

    private function transformsServiceDistributionData($services)
    {
        return $services->filter(function ($service) {
            $total = DirectQueue::where('service_id', $service->id)->whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay()
            ])->count();

            return $total > 0;
        })
            ->map(function ($service) {
                $directQueue = DirectQueue::select(
                    'service_id',
                    DB::raw('CASE
                        WHEN waiting_duration <= 300 THEN \'_0_5\'
                        WHEN waiting_duration BETWEEN 301 AND 600 THEN \'_5_10\'
                        WHEN waiting_duration BETWEEN 601 AND 900 THEN \'_10_15\'
                        WHEN waiting_duration BETWEEN 901 AND 1200 THEN  \'_15_20\'
                        WHEN waiting_duration BETWEEN 1201 AND 1500 THEN \'_20_25\'
                        WHEN waiting_duration BETWEEN 1501 AND 1800 THEN \'_25_30\'
                        WHEN waiting_duration > 1800 THEN \'_30_\'
                    END AS wait_time_distribution,
                    COUNT(*) AS total')
                )
                    ->whereBetween('created_at', [
                        Carbon::now()->startOfDay(),
                        Carbon::now()->endOfDay()
                    ])
                    ->whereIn('status', ['served', 'end served'])
                    ->where('service_id', $service->id)
                    ->groupBy('service_id', 'wait_time_distribution')
                    ->get();
                
                $_0_5 = $directQueue->firstWhere('wait_time_distribution', '_0_5');
                $_5_10 = $directQueue->firstWhere('wait_time_distribution', '_5_10');
                $_10_15 = $directQueue->firstWhere('wait_time_distribution', '_10_15');
                $_15_20 = $directQueue->firstWhere('wait_time_distribution', '_15_20');
                $_20_25 = $directQueue->firstWhere('wait_time_distribution', '_20_25');
                $_25_30 = $directQueue->firstWhere('wait_time_distribution', '_25_30');
                $_30_ = $directQueue->firstWhere('wait_time_distribution', '_30_');

                return [
                    'event_date' => Carbon::yesterday()->format('Y-m-d'),
                    'branch_id' => $service->Department->branch_id,
                    'department_id' => $service->department_id,
                    'service_id' => $service->id,
                    'name' => $service->name,
                    '_0_5' => $_0_5 ? $_0_5->total : 0,
                    '_5_10' => $_5_10 ? $_5_10->total : 0,
                    '_10_15' => $_10_15 ? $_10_15->total : 0,
                    '_15_20' => $_15_20 ? $_15_20->total : 0,
                    '_20_25' => $_20_25 ? $_20_25->total : 0,
                    '_25_30' => $_25_30 ? $_25_30->total : 0,
                    '_30_' => $_30_ ? $_30_->total : 0,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            });
    }

    private function transformsServiceData($services)
    {
        return $services->filter(function ($service) {
            $total = DirectQueue::where('service_id', $service->id)->whereBetween('created_at', [
                Carbon::yesterday()->startOfDay(),
                Carbon::yesterday()->endOfDay()
            ])->count();

            return $total > 0;
        })->map(function ($service) {
            $directQueue = DirectQueue::where('service_id', $service->id)->whereBetween('created_at', [
                Carbon::yesterday()->startOfDay(),
                Carbon::yesterday()->endOfDay()
            ]);
            $servedDirectQueue = $directQueue->clone()->whereIn('status', ['served', 'end served']);
            $endServedDirectQueue = $directQueue->clone()->where('status', 'end served');

            return [
                'event_date' => Carbon::yesterday()->format('Y-m-d'),
                'branch_id' => $service->Department->branch_id,
                'department_id' => $service->department_id,
                'service_id' => $service->id,
                'name' => $service->name,
                'total_queue' => $directQueue->count(),
                'total_served' => $servedDirectQueue->count(),
                'total_no_show' => $directQueue->clone()->where('status', 'no show')->count(),
                'shortest_wait_duration' => $servedDirectQueue->min('waiting_duration'),
                'average_wait_duration' => floor($servedDirectQueue->avg('waiting_duration')),
                'longest_wait_duration' => $servedDirectQueue->max('waiting_duration'),
                'shortest_serve_duration' => $endServedDirectQueue->min('serving_duration'),
                'average_serve_duration' => floor($endServedDirectQueue->avg('serving_duration')),
                'longest_serve_duration' => $endServedDirectQueue->max('serving_duration'),
                'created_at' => date('Y-m-d H:i:s')
            ];
        });
    }

    private function transformsWorkstationData($workstations)
    {
        return $workstations->filter(function ($workstation) {
            $total = DirectQueue::where('workstation_id', $workstation->id)->whereBetween('created_at', [
                Carbon::yesterday()->startOfDay(),
                Carbon::yesterday()->endOfDay()
            ])->count();

            return $total > 0;
        })->map(function ($workstation) {
            $directQueue = DirectQueue::where('workstation_id', $workstation->id)->whereBetween('created_at', [
                Carbon::yesterday()->startOfDay(),
                Carbon::yesterday()->endOfDay()
            ]);
            $servedDirectQueue = $directQueue->clone()->whereIn('status', ['served', 'end served']);
            $endServedDirectQueue = $directQueue->clone()->where('status', 'end served');

            $total_operating_duration = CounterActivity::whereBetween('created_at', [
                Carbon::yesterday()->startOfDay(),
                Carbon::yesterday()->endOfDay()
            ])
                ->where('workstation_id', $workstation->id)
                ->sum('operation_duration');
            $total_serve_duration = $endServedDirectQueue->sum('serving_duration');

            return [
                'event_date' => Carbon::yesterday()->format('Y-m-d'),
                'branch_id' => $workstation->Department->branch_id,
                'department_id' => $workstation->department_id,
                'workstation_id' => $workstation->id,
                'name' => $workstation->name,
                'total_queue' => $directQueue->count(),
                'total_served' => $servedDirectQueue->count(),
                'total_no_show' => $directQueue->clone()->where('status', 'no show')->count(),
                'shortest_wait_duration' => $servedDirectQueue->min('waiting_duration'),
                'average_wait_duration' => floor($servedDirectQueue->avg('waiting_duration')),
                'longest_wait_duration' => $servedDirectQueue->max('waiting_duration'),
                'shortest_serve_duration' => $endServedDirectQueue->min('serving_duration'),
                'average_serve_duration' => floor($endServedDirectQueue->avg('serving_duration')),
                'longest_serve_duration' => $endServedDirectQueue->max('serving_duration'),
                'total_operating_duration' => $total_operating_duration,
                'total_serve_duration' => $total_serve_duration,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        });
    }

    private function transformsVctData($vcts)
    {
        return $vcts->filter(function ($vct) {
            $total = DirectQueue::where('vct_id', $vct->id)->whereBetween('created_at', [
                Carbon::yesterday()->startOfDay(),
                Carbon::yesterday()->endOfDay()
            ])->count();

            return $total > 0;
        })->map(function ($vct) {
            $directQueue = DirectQueue::where('vct_id', $vct->id)->whereBetween('created_at', [
                Carbon::yesterday()->startOfDay(),
                Carbon::yesterday()->endOfDay()
            ]);
            $servedDirectQueue = $directQueue->clone()->whereIn('status', ['served', 'end served']);
            $endServedDirectQueue = $directQueue->clone()->where('status', 'end served');

            $total_operating_duration = CounterActivity::whereBetween('created_at', [
                Carbon::yesterday()->startOfDay(),
                Carbon::yesterday()->endOfDay()
            ])
                ->where('vct_id', $vct->id)
                ->sum('operation_duration');
            $total_serve_duration = $endServedDirectQueue->sum('serving_duration');

            return [
                'event_date' => Carbon::yesterday()->format('Y-m-d'),
                'branch_id' => $vct->branch_id,
                'department_id' => $vct->WorkstationVct->Workstation->department_id,
                'vct_id' => $vct->id,
                'name' => $vct->name,
                'total_queue' => $directQueue->count(),
                'total_served' => $servedDirectQueue->count(),
                'total_no_show' => $directQueue->clone()->where('status', 'no show')->count(),
                'shortest_wait_duration' => $servedDirectQueue->min('waiting_duration'),
                'average_wait_duration' => floor($servedDirectQueue->avg('waiting_duration')),
                'longest_wait_duration' => $servedDirectQueue->max('waiting_duration'),
                'shortest_serve_duration' => $endServedDirectQueue->min('serving_duration'),
                'average_serve_duration' => floor($endServedDirectQueue->avg('serving_duration')),
                'longest_serve_duration' => $endServedDirectQueue->max('serving_duration'),
                'total_operating_duration' => $total_operating_duration,
                'total_serve_duration' => $total_serve_duration,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        });
    }
}
