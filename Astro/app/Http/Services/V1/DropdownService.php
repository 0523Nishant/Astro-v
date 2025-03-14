<?php


namespace App\Http\Services\V1;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DropdownService
{
    public function getList($data = array())
    {
        if(empty($data)){ return false; }
        try {
            $type = !empty($data['type']) ? $data['type'] : 'zone';
            $zone_name = !empty($data['zone_name']) ? $data['zone_name'] : '';
            $state_name = !empty($data['state_name']) ? $data['state_name'] : '';
            $division_name = !empty($data['division_name']) ? $data['division_name'] : '';
            $tehsil = !empty($data['tehsil']) ? $data['tehsil'] : '';
            $register_for = !empty($data['register_for']) ? $data['register_for'] : '';
            if($type == 'zone'){
                return DB::table('TBL_LMS_ZONE_STATES_M')->lock("WITH (nolock)")->select('TX_ZONE')->distinct()->whereNotNull('TX_ZONE')->orderBy('TX_ZONE')->get();
            }
            else if($type == 'states' && !empty($zone_name)){
                return DB::table('TBL_LMS_ZONE_STATES_M')->lock("WITH(NOLOCK)")
                ->where('TX_ZONE', $zone_name)
                ->where(function ($query) {
                    $query->where('TX_TYPE', 'state')
                    ->orWhere('TX_TYPE','both state & ojt');
                })
                ->whereNotNull('TX_STATES')
                ->select('PK_LMS_ZONE_STATES_M_ID','TX_STATES')->distinct()
                ->orderBy('TX_STATES')
                // ->toSql();
                ->get();
            }
            else if($type = 'district' && !empty($state_name)){
                $str_state = explode('-',$state_name);
                $state_id = $str_state[0];
                $state = $str_state[1];
                return DB::table('TBL_LMS_DISTRICT_TAHSIL_M AS dis')->lock("WITH(NOLOCK)")
                ->join("TBL_LMS_ZONE_TO_VILLAGE_D AS map","map.FK_LMS_DISTRICT_TEHSIL_M_ID","=","dis.PK_LMS_DISTRICT_TEHSIL_M_ID","inner")
                ->where("map.FK_LMS_ZONE_STATES_M_ID",$state_id)
                ->select("dis.TX_DISTRICT")->distinct()
                ->orderBy("dis.TX_DISTRICT")
                // ->toSql();
                ->get();
            }
            else if($type = 'sub_division' && !empty($division_name)){
                return DB::table('TBL_LMS_DISTRICT_TAHSIL_M AS dis')->lock("WITH(NOLOCK)")
                ->where("dis.TX_DISTRICT",$division_name)
                ->select("dis.PK_LMS_DISTRICT_TEHSIL_M_ID", "dis.TX_TEHSIL_SUBDIVISION")->distinct()
                ->orderBy("dis.TX_TEHSIL_SUBDIVISION")
                // ->toSql();
                ->get();
            }
            else if($type = 'city' && !empty($tehsil)){
                // Log::info("DropdownService == sub_division  => ".$register_for);
                $str_tehsil = explode('-',$tehsil);
                $tehsil_id = $str_tehsil[0];
                $tehsil = $str_tehsil[1];

                $cities_list = DB::table('TBL_LMS_ZONE_TO_VILLAGE_D as map')->lock("WITH(NOLOCK)")
                ->join(DB::raw("TBL_LMS_CITY_VILLAGE_M as c WITH(NOLOCK)"),'c.PK_LMS_CITY_VILLAGE_M_ID','=','map.FK_LMS_CITY_VILLAGE_M_ID')
                ->where('map.FK_LMS_DISTRICT_TEHSIL_M_ID',$tehsil_id)
                ->select("c.TX_CITY_VILLAGE")
                ->orderBy("c.TX_CITY_VILLAGE")
                // ->toSql();
                ->get();
                if(count($cities_list) <= 0){
                    $cities_list = array(
                        ["TX_CITY_VILLAGE" => $tehsil]
                    );
                }
                return $cities_list;
            }
            else if($type = 'ojt' && !empty($zone_name)){
                // Log::info("DropdownService == ojt  => ".$register_for);
                if($register_for == 'wholesale'){
                    return DB::table('TBL_LMS_ZONE_STATES_M')->lock("WITH(NOLOCK)")
                    ->where('TX_ZONE', $zone_name)
                    ->where(function ($query) {
                        $query->where('TX_TYPE', 'wholesale location')
                              ->orWhere('TX_TYPE','both ojt & wholesale');
                    })
                    ->select('PK_LMS_ZONE_STATES_M_ID','TX_STATES')->distinct()
                    ->orderBy('TX_STATES')
                    // ->toSql();
                    ->get();
                } else {
                    return DB::table('TBL_LMS_ZONE_STATES_M')->lock("WITH(NOLOCK)")
                    ->where('TX_ZONE', $zone_name)
                    ->where(function ($query) {
                        $query->where('TX_TYPE', 'ojt location')
                              ->orWhere('TX_TYPE','both state & ojt')
                              ->orWhere('TX_TYPE','both ojt & wholesale');
                    })
                    ->select('PK_LMS_ZONE_STATES_M_ID','TX_STATES')->distinct()
                    ->orderBy('TX_STATES')
                    // ->toSql();
                    ->get();
                }
            }
        } catch (\Exception $e) {
            Log::error("===  DropdownService  ===  getList  === Error => ".$e);
            throw $e;
        }
    }
}
