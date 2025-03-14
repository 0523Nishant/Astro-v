<?php


namespace App\Http\Services\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataMappingService
{
    public function make(Request $request, $client_id = 0)
    {
        try {
			$defaultNodeId = config('constants.default_node_id_'.$request->register_for);
			if(empty($defaultNodeId)){
                if($request->register_for == 'warehouse'){
                    $defaultNodeId = 'DOM10000003';
                }
                else if($request->register_for == 'wholesale'){
                    $defaultNodeId = 'DOM10000004';
                }
                else if($request->register_for == 'delivery'){
                    $defaultNodeId = 'DOM10000002';
                }
                else if($request->register_for == 'edab'){
                    $defaultNodeId = 'DOM10000001';
                }
            }
            ##############  array to call User create API  ############
            $org_arr = array(
                ["mapby" => "nodeid"],
                ["ids" => [$defaultNodeId]]
            );
            // $role_arr = array(
            //     "Role1",
            //     "Role2"
            // );
            // $indirect_line_manager_arr = array(
            //     "inLMUNQ001",
            //     "inLM@email.com",
            //     "inLMUsername"
            // );
            // $learning_group_arr = array(
            //     "Group1",
            //     "Group2"
            // );
            $where_type = 'W';
            if ($request->register_for == 'delivery') {
                $where_type = 'D';
            } else if ($request->register_for == 'edab') {
                $where_type = 'E';
            } else if ($request->register_for == 'wholesale') {
                $where_type = 'WS';
            }
            $unique_id_res = DB::table("LEARNERM")->lock("WITH(NOLOCK)")
                ->selectRaw("'$where_type'+CAST(CAST(LEFT(SUBSTRING(TX_PSNO, PATINDEX('%[0-9.-]%', TX_PSNO), 8000),PATINDEX('%[^0-9.-]%', SUBSTRING(TX_PSNO, PATINDEX('%[0-9.-]%', TX_PSNO), 8000) + 'X') +1) AS int) + 1 AS nvarchar(100)) AS psno")
                ->where('FK_CLIENTID', $client_id)
                ->whereRaw("(CASE WHEN PATINDEX('%[a-zA-Z]%', REVERSE(TX_PSNO)) > 1 THEN LEFT(TX_PSNO,LEN(TX_PSNO) - PATINDEX('%[a-zA-Z]%', REVERSE(TX_PSNO)) + 1) ELSE '' END) = '$where_type'")
                ->orderByDesc("PK_LEARNERID")
                ->first();
                // ->toSql();
            // echo json_encode($unique_id_res);exit;
            //Log::info("== UNIQUE ID QUERY RES ==> ".json_encode($unique_id_res));
            $unique_id = !empty($unique_id_res) ? $unique_id_res->psno : $where_type . '1';
            // echo $unique_id;exit;

            $sub_division = explode('-',$request->sub_division);
            $state = explode('-',$request->state);
            $user_arr = array(
                "unique_id" => $unique_id,
                "firstname" => $request->first_name,
                "username" => $request->user_email,
                "email" => $request->user_email,
                "status" => "Active",
                "lmsrole" => "LR",
                "org" => $org_arr,
                "lastname" => $request->last_name,
                "password" => "password@0001",
                "gender" => $request->gender,
                // "alternate_email" => "",
                // "phone" => "1234567890",
                "mobile" => $request->mobile,
                "birth_date" => date('Y-m-d',strtotime($request->birth_date)),
                // "joining_data" => "2022-03-12",
                // "termination_date" => "2040-12-31",
                // "user_expiry_date" => "2040-12-31",
                "user_language" => "en",
                // "grade" => "L5",
                // "current_experience" => 1,
                // "total_experience" => 2,
                // "designation" => "Designation01",
                // "role" => $role_arr,
                // "line_manager" => "LMUNQ0001",
                // "indirect_line_manager" => $indirect_line_manager_arr,
                // "learning_group" => $learning_group_arr,
                "local_address" => $request->address,
                // "local_pin_no" => "411108",
                // "pin_no" => "411108",
                "address_line1" => $sub_division[1],
                "address_line2" => $request->city,
                "city" => $request->district,
                "state" => $state[1],
                "country" => ucfirst($request->zone)
            );
            return $user_arr;
            ##############  array to call User create API  ############
        } catch (\Exception $e) {
            Log::error("===  DataMappingService  ===  make == Error Line Number => ".$e->getLine());
            // Log::error("===  DataMappingService  ===  make == Error => ".$e->getMessage());
            throw($e);
        }
    }
}
