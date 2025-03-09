<?php

namespace App\Http\Controllers;

use App\User;
use App\Branch;
use App\Customer;
use App\BranchType;
use App\Models\Regency;
use App\Models\Province;
use App\Models\SGProvince;
use App\Models\SGRegencies;
use App\Models\VNProvinces;
use App\Models\VNRegencies;
use App\RegistrationBranch;
use App\BranchConfiguration;
use Illuminate\Http\Request;
use App\Helpers\AutoPopulate;
use Illuminate\Support\Carbon;
use App\Mail\RegistrationBranchMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Laravel\Socialite\Facades\Socialite;
use App\Mail\Branch\Registration\Verified;
use App\Http\Requests\StoreRegistrationBranch;
use App\Interfaces\BranchTypeRepositoryInterface;

class RegistrationBranchController extends Controller
{
    private BranchTypeRepositoryInterface $branchTypeRepository;

    public function __construct(BranchTypeRepositoryInterface $branchTypeRepository)
    {
        $this->branchTypeRepository = $branchTypeRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRegistrationBranch $request)
    {
        $input = $request->all();

        $input['password'] = Crypt::encryptString($request->password);
        $branch = RegistrationBranch::create($input);

        // sending email
        Mail::to($branch->email)->send(new RegistrationBranchMail($branch));
        return redirect(route('registrationBranch.afterRegister'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RegistrationBranch  $registrationBranch
     * @return \Illuminate\Http\Response
     */
    public function show(RegistrationBranch $registrationBranch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RegistrationBranch  $registrationBranch
     * @return \Illuminate\Http\Response
     */
    public function edit(RegistrationBranch $registrationBranch)
    {
        $input = $registrationBranch->toArray();
        switch ($input['queue_type']) {
            case 'direct_queue':
                $input['branch_type_id'] = $this->branchTypeRepository->getFreeOnsiteLicense()->id;
                break;
            case 'appointment_queue':
                $input['branch_type_id'] = $this->branchTypeRepository->getFreeAppointmentLicense()->id;
                break;
            default:
                $input['branch_type_id'] = $this->branchTypeRepository->getFreeExhibitionLicense()->id;
                break;
        }


        $reg = Regency::with('province')->find($registrationBranch['regency_id']);
        $prov = $reg->province;


        $timeNow = Carbon::now();
        $licenseExpirationDay = config('app.license_expiration_day');

        // duplicate to branches table
        $input['timezone'] = $prov ? $prov->timezone : null;
        $input['mobile_phone'] = $registrationBranch->phone;
        $input['license_expiration_date'] = $timeNow->addDays($licenseExpirationDay);
        $branch = Branch::create($input);
        
        // create branch configuration
        BranchConfiguration::create([
            'branch_id' => $branch->id,
            'maximum_recall' => 2,
            'maximum_requeue_count' => 2,
            'allow_transfer' => false
        ]);

        // sending email
        Mail::to($branch->email)->send(new Verified($branch));

        // duplicate to users table
        $input['password'] = Crypt::decryptString($registrationBranch->makeVisible('attribute')->password);
        $input['role'] = 'admin_branch';
        $input['branch_id'] = $branch->id;
        $input['email_verified_at'] = date('Y-m-d H:i:s');
        $user = User::create($input);

        // remove registration branch
        $registrationBranch->is_email_verified = 1;
        $registrationBranch->save();
        $registrationBranch->delete();

        AutoPopulate::create($branch->id);
        return redirect(route('registrationBranch.afterVerified'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RegistrationBranch  $registrationBranch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RegistrationBranch $registrationBranch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RegistrationBranch  $registrationBranch
     * @return \Illuminate\Http\Response
     */
    public function destroy(RegistrationBranch $registrationBranch)
    {
        //
    }

    public function afterRegister()
    {
        return view('afterRegister');
    }

    public function afterVerified()
    {
        return view('afterVerified');
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('google')->user();

        return [
            'id' => $user->getId(),
            'name' => $user->getName(), 
            'token' => $user->token
        ];
    }
}
