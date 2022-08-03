<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Department;
use App\DirectQueue;
use App\Models\CounterActivity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Service;
use App\Workstation;
use App\User;

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
        if (Schema::hasTable(date('Y_m_') . 'department_report')) {
            $departments = Department::all();

            $data = $this->transformsDepartmentData($departments);

            DB::table(date('Y_m_') . 'department_report')->insert($data->toArray());
        }

        if (Schema::hasTable(date('Y_m_') . 'service_report')) {
            $services = Service::with('Department')->get();

            $data = $this->transformsServiceData($services);

            DB::table(date('Y_m_') . 'service_report')->insert($data->toArray());
        }

        if (Schema::hasTable(date('Y_m_') . 'workstation_report')) {
            $workstations = Workstation::with('Department')->get();

            $data = $this->transformsWorkstationData($workstations);

            DB::table(date('Y_m_') . 'workstation_report')->insert($data->toArray());
        }

        if (Schema::hasTable(date('Y_m_') . 'vct_report')) {
            $users = User::whereHas('WorkstationVct')->get();

            $data = $this->transformsVctData($users);

            DB::table(date('Y_m_') . 'vct_report')->insert($data->toArray());
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
