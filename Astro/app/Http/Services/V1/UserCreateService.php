<?php


namespace App\Http\Services\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserCreateService
{
    public function isUserExist($email, $client_id)
    {
        if(empty($email) || empty($client_id)){ return false; }
        try {
            $res = DB::table('LEARNERM')
            ->where('TX_EMAIL',$email)
            ->where('FK_CLIENTID', $client_id)
            ->select("PK_LEARNERID")
            ->first();
            if(!empty($res) && !empty($res->PK_LEARNERID)){
                return true;
            } else { return false; }
        } catch (\Exception $e) {
            Log::error("===  UserCreateService  ===  isUserExist  === Error => ".$e->getMessage());
            throw $e;
        }
    }

    public function isMobileExist($mobile, $client_id)
    {
        Log::info("== Check mobile exist == ".$mobile);
        if(empty($mobile) || empty($client_id)){ return false; }
        try {
            $res = DB::table('LEARNERM AS l')->lock("WITH(NOLOCK)")
            ->join("LRNMYPROFILE AS p","p.fk_learnerid","=","l.PK_LEARNERID")
            ->where('p.TX_MOBILE',$mobile)
            ->where('l.FK_CLIENTID', $client_id)
            ->selectRaw("l.PK_LEARNERID")
            ->first();
            // ->toSql();
            Log::info("=== User exist with mobile Query res => ".json_encode($res));
            if(!empty($res) && !empty($res->PK_LEARNERID)){
                return true;
            } else { return false; }
        } catch (\Exception $e) {
            Log::error("===  UserCreateService  ===  isUserExist  === Error => ".$e->getMessage());
            throw $e;
        }
    }

    public function create(Request $request, $client_id)
    {
        if(empty($client_id)){
            Log::error("===  UserCreateAPIService  === create  == Error => Client ID is empty");
            throw new \Exception("Invalid Portal key", 1);
        }
        try {
            $email = $request->user_email;
            $res = DB::table('LEARNERM')->select('PK_LEARNERID')->where('TX_EMAIL',$email)->where('FK_CLIENTID',$client_id)->first();
            $learner_id = !empty($res) ? $res->PK_LEARNERID : '';

            $location_exp_month = $internship_exp_month = "NULL";
            // Log::info("All dates".$request->location_from_date."  === ".$request->location_to_date);
            if(!empty($request->location_from_date) && $request->location_to_date){
                $location_from_date = Carbon::createFromFormat('Y-m', $request->location_from_date);
                $location_to_date = Carbon::createFromFormat('Y-m', $request->location_to_date);
                $location_exp_month = $location_to_date->diffInMonths($location_from_date);
            }
            if(!empty($request->internship_from_date) && !empty($request->internship_to_date)){
                $internship_from_date = Carbon::createFromFormat('Y-m', $request->internship_from_date);
                $internship_to_date = Carbon::createFromFormat('Y-m', $request->internship_to_date);
                $internship_exp_month = $internship_to_date->diffInMonths($internship_from_date);
            }
            $special_ability = !empty($request->special_ability_text) ? $request->special_ability_text : $request->special_ability;
            $special_ability = !empty($special_ability) ? $special_ability : "NULL";
            $insertArr = array(
                // "FK_LEARNERID" => $learner_id,
                "TX_FATHER_NAME" => $request->father_name,
                "TX_FATHER_OCCUPATION" => $request->father_occupation,
                "TX_MOTHER_NAME" => $request->mother_occupation,
                "TX_INSTITUTE_NAME" => $request->college_name,
                "TX_HIGHEST_EDUCATION" => $request->qualification,
                "TX_HIGHEST_EDUCATION_STATUS" => $request->edu_status,
                "TX_CURRENT_EMPLOYMENT" =>  $request->emp_status,
                "TX_CURRENT_EMPLOYMENT_STATUS" =>  !empty($request->emp_status_value) ? $request->emp_status_value : "NULL",
                "TX_EX_FLIPKART_EMP" => $request->work_with_flipkart,
                "TX_WORK_LOCATION" => isset($request->location_name) ? $request->location_name : "NULL",
                "TX_WORK_EXPERIENCE" => $location_exp_month,
                "TX_FLIPKART_INTERNSHIP" => $request->internship_with_flipkart,
                "TX_FLIPKART_INTERNSHIP_NAME" => isset($request->internship_name) ? $request->internship_name : "NULL",
                "TX_FLIPKART_INTERNSHIP_EXPERIENCE" => $internship_exp_month,
                "TX_WILLING_TO_JOIN_INTERNSHIP" => $request->internship,
                "TX_OFFLINE_INTERNSHIP_PROGRAM" => $request->internship_prog,
                "TX_OJT_LOCATION" => isset($request->ojt_location) ? $request->ojt_location : "NULL",
                "TX_DRIVING_LICENCE" => isset($request->driving_license) ? $request->driving_license : "NULL",
                "TX_OWN_BIKE" => isset($request->have_bike) ? $request->have_bike : "NULL",
                "TX_COMFORT_TRAVEL" => isset($request->confirm_to_travel) ? $request->confirm_to_travel : "NULL",
                "TX_KNOWLEGDE_OF_COMPUTER_AND_EXCEL" => isset($request->excel_knowledge) ? $request->excel_knowledge : "NULL",
                "TX_SPECIAL_ABILITY" => $special_ability,

                #######  added for report  #######
                "TX_LOCATION_FROM_DATE" => !empty($request->location_from_date) ? $request->location_from_date : 'NULL',
                "TX_LOCATION_TO_DATE" => !empty($request->location_to_date) ? $request->location_to_date : 'NULL',
                "TX_INTERNSHIP_FROM_DATE" => !empty($request->internship_from_date) ? $request->internship_from_date : 'NULL',
                "TX_INTERNSHIP_TO_DATE" => !empty($request->internship_to_date) ? $request->internship_to_date : 'NULL',
                #######  added for report  #######

                // "FK_CLIENTID" => $client_id
            );

            // $insertArr = Arr::where($insertArr, function ($value) {
            //     if(Str::contains($value, "'") && !empty($value)){
            //         $value = str_replace("'","''",$value);
            //     }
            //     $value = strip_tags($value);
            //     return (empty($value) ? $value = "Hello" : DB::raw($value));
            // });
            // $collection = collect($insertArr);
            // $insertArr = $collection->filter(function($value){
            //     // if(Str::contains($value, "'") && !empty($value)){
            //         $value = str_replace("'","''",$value);
            //     // }
            //     $value = strip_tags($value);
            //     $value = (empty($value) ? "NULL" : $value);
            //     return $value;
            // });
            // echo json_encode($insertArr);exit;

            // $id = DB::table('TBL_LMS_FLIPKART_ADDITIONAL_FIELDS')->insertGetId($insertArr);
            DB::table('TBL_LMS_FLIPKART_ADDITIONAL_FIELDS')->updateOrInsert(["FK_LEARNERID" => $learner_id,"FK_CLIENTID" => $client_id],$insertArr);

            return DB::table('LEARNERM')->where('PK_LEARNERID',$learner_id)->update(['TX_FLIPKART_REG_TYPE' => $request->register_for]);
            // return ($id)?true:false;
            // return true;

        } catch (\Exception $e) {
            Log::error("===  UserCreateAPIService  === create  == Error => ".$e->getMessage());
            throw $e;
        }
    }
}
