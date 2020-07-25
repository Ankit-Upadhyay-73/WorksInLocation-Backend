<?php

namespace App\Http\Controllers;
use App\workers;
use App\WorkersProfile;
use Illuminate\Http\Request;
use Illuminate\Encryption\Encrypter;
class WorkersController extends Controller
{
    //
    public function login(Request $request)
    {
        $header =  $request->header('auth-key');
        if($header!=null)
        {
            $isExists = WorkersProfile::select('contactNo')->where('contactNo',$header)->get();
            if(count($isExists)==0)
            {
                $newWorker = new workers;
                $newWorker->contactNo = $header;
                $newWorker->save();    
                return true;    
            }
            return false;
        }
        return false;
    }
}
