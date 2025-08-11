<?php

namespace App\Http\Controllers\API;

use App\Branch;
use App\Service;
use App\DirectQueue;
use App\BranchConfiguration;
use App\Models\SecretKeyAPi;
use App\Models\SurveyQuestions;
use App\Models\SurveyResponses;
use App\Events\OnsiteQueueUpdated;
use App\Models\SurveyConfiguration;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Events\DirectQueue as DirectQueueEvent;
use App\Interfaces\DirectQueueRepositoryInterface;
use App\Events\VCTDirectQueue as VCTDirectQueueEvent;
use App\Http\Resources\DirectQueue\Detail as DirectQueueDetail;
use App\Http\Requests\API\DirectQueue\Store as DirectQueueStore;
use App\Http\Requests\API\DirectQueue\Feedback as DirectQueueFeedback;

class DirectQueueController extends Controller
{
    private DirectQueueRepositoryInterface $onsiteRepository;

    public function __construct(DirectQueueRepositoryInterface $onsiteRepository)
    {
        $this->onsiteRepository = $onsiteRepository;
    }

    public function index(Branch $branch)
    {
        $services = Service::where('branch_id', $branch->id)
            ->get();

        foreach ($services as $service) {
            $service->total_queue = DirectQueue::where('service_id', $service->id)
                ->whereDate('created_at', date('Y-m-d'))
                ->where('status', 'waiting')
                ->count();
        }

        return response()->json([
            'success' => true,
            'message' => 'get direct queue services by branch id',
            'data' => $services
        ]);
    }

    public function show(DirectQueue $directQueue)
    {
        return response()->json([
            'success' => true,
            'message' => 'get detail direct queues',
            'data' => new DirectQueueDetail($directQueue)
        ]);
    }

    public function store(DirectQueueStore $request)
    {
        try {
            $service = Service::find($request->service_id);

            $data = $request->all();
            $data['client_id'] = $request->cookie('client_id');
            $data['direct_queue_channel'] = 'Mobile Apps';

            $directQueue = $this->onsiteRepository->store($data);

            // send event to update Direct Queue Monitor
            event(new VCTDirectQueueEvent($directQueue, $service->branch_id));
            event(new DirectQueueEvent($directQueue, $service->branch_id));

            if ($directQueue->client_id) {
                event(new OnsiteQueueUpdated($directQueue));
            }

            $branch = Branch::where('id',$directQueue->branch_id)->first();
            $client = BranchConfiguration::where('branch_id',$directQueue->branch_id)->first();
            $tokenAPI = SecretKeyAPi::where('branch_id', $directQueue->branch_id)->first();
            $webhookMessage = "You need an Webhook Url or Activate the feature!";

            if ($client->webhook_url && $tokenAPI->secret_token && $tokenAPI->is_active){
                $webhookMessage = "Webhook Send!";
                $webhookData = [
                    'event_type' => 'onsite_create_booking',
                    
                    'user' => (object)[
                        'name' => $directQueue->name,
                        'phone' => $directQueue->phone,
                        'email' => $directQueue->email,
                        'created_at' => $directQueue->created_at,
                    ],
                    'queue' => (object)[
                        'id' => $directQueue->id,
                        'service_id' => $directQueue->service_id,
                        'service_name' => $directQueue->service_name,
                        'service_type' => 'Direct Queue',
                        'created_at' => $directQueue->created_at,
                        'booking_code' => $directQueue->booking_code,
                        'branch_id' => $directQueue->branch_id,
                        'branch_name' => $branch->name,
                    ]
                ];
                $webhookData = (object) $webhookData;

                $this->sendWebhook($client, $webhookData);
                
            }else{
                $webhookMessage = "There's no Webhook Url/The feature was inactive";
            }
            

            return response()->json([
                'success' => true,
                'message' => 'direct queue created',
                'data' => $directQueue,
                'Webhook' => $webhookMessage,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function upcoming()
    {
        $directQueues = DirectQueue::whereUserId(Auth::id())
            ->whereIn('status', ['waiting', 'served', 'requeue'])
            ->whereDate('created_at', '>=', date('Y-m-d'))
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'get upcoming direct queues',
            'data' => DirectQueueDetail::collection($directQueues)
        ]);
    }

    public function feedback(DirectQueue $directQueue, DirectQueueFeedback $request)
    {
        $branchId = $directQueue->branch_id;
        $survey_config = SurveyConfiguration::where('branch_id', $branchId)->first();

        if($survey_config && $survey_config->type !== 'default'){
            $totalRating = 0;
            if (is_array($request->rating)) {
                foreach ($request->rating as $questionId => $rateValue) {
                    SurveyResponses::updateOrCreate(
                        [
                            'direct_queue_id'   => $directQueue->id,
                            'survey_question_id' => $questionId,
                            'survey_type' => $survey_config->type,
                        ],
                        [
                            'value' => $rateValue,
                            'survey_config_id' => $survey_config->id,
                            'branch_id' => $branchId,
                            'queue_type' => 'onsite' ,
                            'name' => $directQueue->name ?? 'no name',
                            'email' => $directQueue->email ?? 'no email',

                        ]
                    );
                    $totalRating += $rateValue;
                }
                
                $directQueue->update([
                    'rating' => $totalRating,
                    'is_liked' => $request->is_liked,
                ]);
            } else {
                $firstQuestionId = SurveyQuestions::where('survey_config_id', $survey_config->id)
                    ->value('id');

                SurveyResponses::updateOrCreate(
                    [
                        'direct_queue_id'    => $directQueue->id,
                        'survey_question_id' => $firstQuestionId,
                        'survey_type' => $survey_config->type,
                    ],
                    [
                        'value' => $request->rating,
                        'survey_config_id' => $survey_config->id,
                        'branch_id' => $branchId,
                        'queue_type' => 'onsite' ,
                        'name' => $directQueue->name ?? 'no name',
                        'email' => $directQueue->email ?? 'no email',
                    ]
                );
                $directQueue->update([
                    'rating' => $request->rating,
                    'is_liked' => $request->is_liked,
                ]);
            }
             return response()->json([
                'success' => true,
                'message' => 'success give feedback direct queue',
                'data' => $directQueue
            ]);
        }

        $directQueue->update([
            'rating' => $request->rating,
            'is_liked' => $request->is_liked,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'success give feedback direct queue',
            'data' => $directQueue
        ]);
    }

    protected function sendWebhook($client, $webhookData)
    {
     
        $guzzle = new \GuzzleHttp\Client();  
        $tokenAPI = SecretKeyAPi::where('branch_id', $client->branch_id)->first();
       

        try {

            $response = $guzzle->post($client->webhook_url, [
                'headers' => [
                    'x-secret-token' => $tokenAPI->secret_token,
                    'Content-Type' => 'application/json',
                ],
                'json' => $webhookData
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Webhook failed with status: ' . $response->getStatusCode());
            }

            return response()->json([
                'status' => 'success',
               ]);

        } catch (\Exception $e) {
           return response()->json([
            'status' => 'error',
            'message' =>  $e->getMessage()
           ]);
        }
    }

}
