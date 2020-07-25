<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs;
use App\RunningJobs;
class JobsController extends Controller
{
    //
    public function addJob(Request $request)
    {
        $header = $request->header('auth-key');
        $id =strval(rand(1000,10000000));
        $val = Jobs::select('id')->get();
        while(true)
        {
            foreach($val as $i)
            {
                if($id==$i)
                  {
                    rand(1000,10000000);
                    continue;
                  }  
            } 
            break;
        }


        if($header!=null)
        {
            $job = new Jobs();
            $job->id = $id;
            $job->contactNo = $header;
            $job->location = $request->input('jobLocation');
            $job->noOfWorkers = $request->input('noOfWorkers');
            $job->jobOnDate = $request->input('jobOnDate');
            $job->jobName = $request->input('jobName');
            $job->others = $request->input('others');
            $job->isClosed = "false";
            $isAdded =  $job->save();            
            if($isAdded)
                return 1;
            return -1;
        }
        return 0;
    }

    public function showJobs(Request $request,$city,$district,$state)
    {
        $header = $request->header('auth-key');
        $allJobs = array();
        if($header!=null)
        {
            $job  = Jobs::select('jobOnDate','jobName','contactNo','location','others','id','noOfWorkers')->where('isClosed',"false")->get();        
            for($i=0;$i<count($job);$i++) 
            {
                $jobId =  $job[$i]['id'];
                $contact = $job[$i]['contactNo'];
                $activeJob  = Jobs::select('jobOnDate','jobName','contactNo','location','others','id','noOfWorkers')->where('id',$jobId)->get();        
                
                //denying self added jobs
                if($contact==$header)
                    continue;

                $noOfWorkers = $job[$i]['noOfWorkers'];
                $appliedUsers = RunningJobs::select('applicants','AppliedDate')->where(['jobId'=>$jobId,'AppliedDate'=>$activeJob[0]['jobOnDate'],'applicants'=>$header])->get();
                //Rejecting Already Applied Jobs
                if(count($appliedUsers)!=0)
                {
                    continue;
                }
                
                if ($noOfWorkers<=sizeof($appliedUsers))
                {
                    continue;
                }

                $jobName = $activeJob[0]['jobName'];
                $jobOndate = $activeJob[0]['jobOnDate'];
                $location = $activeJob[0]['location'];
                $others = $activeJob[0]['others'];
                $id = $activeJob[0]['id'];
                $noOfWorkers =  $activeJob[0]['noOfWorkers'];
                if(count($appliedUsers)<$noOfWorkers)
                {
                    $jb = explode(",",$job[$i]['location']);
                    if(($jb[0]==$city) && ($jb[1]==$district) && ($jb[2]==$state)) 
                    {
                        $allJobs[count($allJobs)]=['jobName'=>$jobName,'jobOnDate'=>$jobOndate,'location'=>$location,'others'=>$others,'id'=>$id,'noOfWorkers'=>$noOfWorkers];
                    }
                    else
                    {
                        if(($jb[1]==$district) && ($jb[2]==$state)) 
                        {
                            $allJobs[count($allJobs)]=['jobName'=>$jobName,'jobOnDate'=>$jobOndate,'location'=>$location,'others'=>$others,'id'=>$id,'noOfWorkers'=>$noOfWorkers];
                        }
                        else
                        {
                            if(($jb[0]==$city)  && ($jb[2]==$state)) 
                            {
                                $allJobs[count($allJobs)]=['jobName'=>$jobName,'jobOnDate'=>$jobOndate,'location'=>$location,'others'=>$others,'id'=>$id,'noOfWorkers'=>$noOfWorkers];
                            }            
                        }
                    }        
                }
            }                

            if(sizeof($allJobs)!=0)
            {
                return $allJobs;
            }
        }
    }

    public function uploadedJobs(Request $request)   
    {
        $header = $request->header('auth-key');
        if ($header!=null)
        {
            $jobs = Jobs::select('jobOnDate','jobName','location','others','id','isClosed')->where('contactNo',$header)->get();
            return $jobs;
        }
        return 0;
    }

    public function appliedJobs(Request $request)
    {
        $header = $request->header('auth-key');
        $allAppliedJobs = [];
        if ($header!=null)
        {
            $jobId = RunningJobs::select('jobId')->where('applicants',$header)->get();
            if(sizeof($jobId)!=0)
            {
                for($j = 0;$j<sizeof($jobId);$j++)
                {
                    $jobs = Jobs::select('jobOnDate','jobName','location','others','id','isClosed')->where('id',$jobId[$j]['jobId'])->get();            
                    $allAppliedJobs[$j] = $jobs[0];
                    
                }
                return $allAppliedJobs;
            }
            return 0;
        }
        return 0;
    }

    public function isAlreadyWorking(Request $request,$date)
    {
        $header = $request->header('auth-key');
        $isWorking = false;
        if ($header!=null)
        {
            $jobs = RunningJobs::select('AppliedDate','jobId')->where('applicants',$header)->get();
            for($i = 0;$i<sizeof($jobs);$i++)
            {
                $isWorkingJobClosed = Jobs::select('isClosed')->where('id',$jobs[$i]['jobId'])->get();
                if($jobs[$i]['AppliedDate']==$date && $isWorkingJobClosed[0]['isClosed']=='false')
                {
                    $isWorking = true;
                }
            }
            if(!$isWorking)
                return 1;
            return 0;
        }
        return -1;
    }

    public function applyOnJob(Request $request,$id,$date)
    {
        $header = $request->header('auth-key');
        if ($header!=null)
        {
            $isAlreadyAppliedOnDate = RunningJobs::select('jobId','AppliedDate')->where('applicants',$header)->get();                                    
            // for($i=0;$i<count($isAlreadyAppliedOnDate);$i++)
            // {
            //     if($isAlreadyAppliedOnDate[$i]['AppliedDate']==$date)
            //     {
            //         $isJobClosed =  Jobs::select('isClosed')->where('id',$isAlreadyAppliedOnDate[$i]['jobId']);
            //         if(sizeof($isJobClosed)!=0)
            //         {
            //             if($isJobClosed[0]['isClosed']=='true')
            //                return -1;
            //         }
            //     }
            // }

            $noOfRequiredWorkers = Jobs::select('noOfWorkers')->where('id',$id)->get();
            $workers = $noOfRequiredWorkers[0]['noOfWorkers'];
            $noOfAppliedWorkers = RunningJobs::select('applicants')->where('jobId',$id)->get();
            if(count($noOfAppliedWorkers)<$workers)
            {
                $isJobExists = Jobs::select('isClosed')->where('id',$id)->get();          
                if($isJobExists[0]['isClosed']=="false")
                {
                    $runJob = new RunningJobs;
                    $runJob->jobId = $id;
                    $runJob->applicants = $header;
                    $runJob->AppliedDate = $date;
                    $isSaved = $runJob->save();
                    if($isSaved)
                        return 1;                        
                }
                return 0;                
            }
            return -1;
        }
    }

    public function deletejob($id)
    {
        $val = Jobs::where('id',$id)->update(array('isClosed'=>'true'));
        if($val)
        {
            return 1;
        }
        return 0;
    }

    public function onCancelJob(Request $request,$jobId)
    {
        $header = $request->header('auth-key');
        if($header!=null)
        {
            return route('/show-jobs');
        }
    }

    public function removeJobs(Request $request)
    {
        $mytime = Carbon\Carbon::now();
        $dt = $mytime->toDateString();
        $curDates =  $dt.explode('-');
        $jobs =  Jobs::select('jobOndate','id')->where('isClosed','false');
        for($i = 0;$i<sizeof($jobs);$i++)
        {
            $jobDates =  $jobs[$i]['jobOnDate'];
            $dates =  $jobDates.explode('-');
            if(($dates[0]==$curDates[0]) || ($dates[0].compareTo($curDates[0])>0))
            {
                if($dates[1]==$curDates[1] || ($dates[1].compareTo($curDates[1])>0))
                {
                    if($dates[2]==$curDates[2] || ($dates[2].compareTo($curDates[2])>0))
                        continue;
                }
                else
                {
                    Jobs::where('id',$jobs[$i]['id'])->update(array('isClosed'=>'true'));
                }     
            }
            else
            {
                Jobs::where('id',$jobs[$i]['id'])->update(array('isClosed'=>'true'));
            } 
        }
    }
}
