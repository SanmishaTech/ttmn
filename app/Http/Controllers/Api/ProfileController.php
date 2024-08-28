<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\ProfileNumberService;
use App\Http\Resources\ProfileResource;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Controllers\Api\BaseController;

  /**
     * @group Profile Management
     *
     * API for Managing Profile data
     */

class ProfileController extends BaseController
{
       /*
       * All Profile
       */
    public function index(): JsonResponse
    {
       // $user = Auth::user();
        $profile = Profile::all();
        return $this->sendResponse(['ProfileList'=>ProfileResource::collection($profile)], "All Profiles Retrived successfully");
    }

      /*
       * Get Profiles
       */
      public function getProfiles(string $id): JsonResponse
      {
         // $user = Auth::user();
          $profile = Profile::where('parent_id',$id)->whereNotNull('profile_no')->get();
          return $this->sendResponse(['Profiles'=>ProfileResource::collection($profile)], " Profiles Retrived successfully");
      }

       /*
       * Get Profile By Profile No
       */
      public function getProfileByProfileNo(string $profile_no): JsonResponse
      {
         // $user = Auth::user();
         Log::info('Fetching profile for profile_no: ' . $profile_no);
          $profile = Profile::where('profile_no',$profile_no)->get();
          return $this->sendResponse(['Profiles'=>ProfileResource::collection($profile)], "Profiles Retrived successfully");
      }


     /**
     *  Show Profile
     */
    public function show(string $id): JsonResponse
    {
        $profile = Profile::find($id);

        if (!$profile) {
            return $this->sendError('Profile not found.', ['error'=>'Profile not found']);
        }
        $user = Auth::user();
        // if($user->id !== $profile->user_id){
        //     return $this->sendError('Unauthorized', ['error'=>'You are not allowed to view this profile.']);
        // }
        
        return $this->sendResponse(['profile'=>new ProfileResource($profile),'user'=>$user], 'Profile retrieved successfully.');
    }


     /**
     * Update Profile
     * @bodyParam name string The name of the user.
     * @bodyParam mobile string The mobile number of the user.
     * @bodyParam pan string The pan number of the user.
     * @bodyParam address_1 string The address_1 of the user.
     * @bodyParam address_2 string The address_1 of the user.
     * @bodyParam city string The city of the user.
     * @bodyParam state string The state of the user.
     * @bodyParam pincode string The pincode of the user.
     * @bodyParam bank_name string The bank name of the user.
     * @bodyParam account_name string The account name of the user.
     * @bodyParam account_no string The account number of the user.
     * @bodyParam ifsc string The ifsc number of the user.
     * @bodyParam business_name string The business_name of the user.
     * @bodyParam gstin string The gstin of the user.
     */
    public function update(UpdateProfileRequest $request, string $id): JsonResponse
    {
        $profile = Profile::find($id);
        $user = User::find($profile->user_id);
       // if ($user->hasPermissionTo('update profile')) {
            
            $profile->name = $request->input('name');
            $profile->mobile = $request->input('mobile');
            $profile->pan = $request->input('pan');
            $profile->parent_id = $request->input('parent_id');
            $profile->ref_id = $request->input('ref_id');
            $profile->registration_date = $request->input('registration_date');
            $profile->address_1 = $request->input('address_1');
            $profile->address_2 = $request->input('address_2');
            $profile->city = $request->input('city');
            $profile->state = $request->input('state');
            $profile->pincode = $request->input('pincode');
            $profile->bank_name = $request->input('bank_name');
            $profile->account_name = $request->input('account_name');
            $profile->account_no = $request->input('account_no');
            $profile->ifsc = $request->input('ifsc');
            $profile->business_name = $request->input('business_name');
            $profile->gstin = $request->input('gstin');
            if($request->input('pan_verified')){
                $profile->pan_verified = $request->input('pan_verified');
            }
            if($request->input('bank_verified')){
                $profile->bank_verified = $request->input('bank_verified');
            }
            if($request->input('gstin_verified')){
                $profile->gstin_verified = $request->input('gstin_verified');
            }
            $profile->save();
    
            return $this->sendResponse(['Profile'=>new ProfileResource($profile)], 'Profile updated successfully.');
    
      //  }


    }

    /**
     *  Get Current Month Count
     */
    public function getCurrentMonthCount(): JsonResponse
    {
          $profile_id = auth()->user()->profile->id;

          $startOfMonth = now()->startOfMonth()->toDateString();
          $endOfMonth = now()->endOfMonth()->toDateString();
           
          $count = Profile::where('id', '>', $profile_id)->whereBetween('registration_date',[$startOfMonth, $endOfMonth])->whereNotNull('profile_no')->count();
          
          return $this->sendResponse(['Count'=>$count], 'Count Retrived successfully');
    }
       

}
