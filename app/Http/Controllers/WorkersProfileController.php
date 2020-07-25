<?php

namespace App\Http\Controllers;
use App\WorkersProfile;
use App\RunningJobs;
use App\Workers;
use App\Jobs;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;

class WorkersProfileController extends Controller
{
    //
    public function isLocationSet(Request $request)
    {
        $header = $request->header('auth-key');
        if($header!=null)
        {
            $value =  WorkersProfile::where('contactNo',$header)->get();
            if(count($value)!=0)
            {
                return true;
            }
            return false;
        }
        return false;
    }
    
    public function addLocation(Request $request)
    {
        $header = $request->header('auth-key');
        if($header!=null)
        {
            $workersProfile = new WorkersProfile;
            $workersProfile->contactNo = $header;
            $workersProfile->state = $request->input('state_value');
            $workersProfile->city = $request->input('city_value');
            $workersProfile->district = $request->input('district_value');
            $isSave =  $workersProfile->save();
            if ($isSave)
                return true;
            return false;                
        }
        return false;
    }

    public function updateLocation(Request $request)
    {
        $header = $request->header('auth-key');
        if($header!=null)
        {
            $isUpdated  = WorkersProfile::where('contactNo',$header)->update(array('state'=>$request->input('state_value'),'district'=>$request->input('district_value'),'city'=>$request->input('city_value')));
            if ($isUpdated>0)
                return true;
            return false;                
        }
        return false;
    }

    public function returnLocation(Request $request)
    {
        $header = $request->header('auth-key');
        if ($header!=null)
        {
            $location =  WorkersProfile::select('state','district','city')->where('contactNo',$header)->get();
            return $location;
        }
    }

    public function updateContact(request $request)
    {
        $header = $request->header('auth-key');
        if($header!=null)
        {
            Workers::where('contactNo',$header)->update(array('contactNo'=>$request->input('updatedContactNo')));
            RunningJobs::where('applicants',$header)->update(array('applicants'=>$request->input('updatedContactNo')));
            Jobs::where('contactNo',$header)->update(array('contactNo'=>$request->input('updatedContactNo')));            
            WorkersProfile::where('contactNo',$header)->update(array('contactNo'=>$request->input('updatedContactNo')));                        
        }
    }

}

