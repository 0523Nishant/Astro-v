<?php

namespace App\Http\Services\V1\Reports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetUserDetails
{
    public function getInfo($client_id = null)
    {
        if(empty($client_id)){ return false; }
        try {
            $sql = " select ROW_NUMBER() over (order by l.TX_PSNO)[sr_no],
                l.TX_PSNO as [unique_id],
                (isnull(l.TX_FIRSTNAME,'')+' '+ isnull(l.TX_LASTNAME,'')) [name],
                l.TX_EMAIL as [email_id],
                lmp.TX_MOBILE as [mobile_number],
                lmp.tx_gender as [gender],
                lmp.dt_birthday as [date_of_birth],
                tlfaf.TX_FATHER_NAME as [father_name],
                tlfaf.TX_FATHER_OCCUPATION as [father_occupation],
                tlfaf.TX_MOTHER_NAME as [mother_occupation],
                lmp.TX_COUNTRY as [zone],
                lmp.TX_STATE as [state],
                lmp.TX_CITY as [district],
                lmp.TX_ADDRESSLINE2 as [city],
                lmp.TX_ADDRESSLINE1 as [tehsil],
                lmp.tx_localaddressline1 as [current_address],
                tlfaf.TX_HIGHEST_EDUCATION as[highest_education_qualification],
                tlfaf.TX_INSTITUTE_NAME as[college_name],
                NULL [where_completed_higher_education],
                tlfaf.TX_HIGHEST_EDUCATION_STATUS as [highest_education_status],
                tlfaf.TX_CURRENT_EMPLOYMENT_STATUS as [current_employment_status],
                tlfaf.TX_EX_FLIPKART_EMP as [worked_with_flipkart],
                tlfaf.TX_WORK_LOCATION as [work_location_name],
                tlfaf.TX_WORK_EXPERIENCE as [working_period],
                tlfaf.TX_FLIPKART_INTERNSHIP as [internship_in_flipkart],
                tlfaf.TX_FLIPKART_INTERNSHIP_NAME as [internship_name],
                tlfaf.TX_FLIPKART_INTERNSHIP_EXPERIENCE as [internship_period],
                tlfaf.TX_WILLING_TO_JOIN_INTERNSHIP	as [internship_outside_city],
                tlfaf.TX_OFFLINE_INTERNSHIP_PROGRAM	as [offline_internship_prog],
                tlfaf.TX_OJT_LOCATION as [ojt_location],
                tlfaf.TX_DRIVING_LICENCE as [have_driving_licence],
                tlfaf.TX_OWN_BIKE as [have_bike],
                tlfaf.TX_COMFORT_TRAVEL as [comfortable_to_travel],
                tlfaf.TX_KNOWLEGDE_OF_COMPUTER_AND_EXCEL as [excel_knowledge],
                tlfaf.TX_SPECIAL_ABILITY as [special_ability],
                tlfaf.TX_LOCATION_FROM_DATE as [location_from_date],
                tlfaf.TX_LOCATION_TO_DATE as [location_to_date],
                tlfaf.TX_INTERNSHIP_FROM_DATE as [internship_from_date],
                tlfaf.TX_INTERNSHIP_TO_DATE as [internship_to_date],
                l.DT_CREATEDON as [created_at],
                l.TX_STATUS as [status]
                from LEARNERM l with(nolock)
                INNER JOIN LRNMYPROFILE lmp with(nolock) ON lmp.fk_learnerid=l.PK_LEARNERID and lmp.fk_clientid=l.FK_CLIENTID
                INNER JOIN TBL_LMS_FLIPKART_ADDITIONAL_FIELDS tlfaf WITH(NOLOCK) ON tlfaf.FK_LEARNERID=l.PK_LEARNERID and tlfaf.FK_CLIENTID=l.FK_CLIENTID
                WHERE l.FK_CLIENTID = ? ";
            return DB::select(DB::raw($sql), [$client_id]);
        } catch (\Exception $e) {
            // Log::error("==== Reports ==  GetUserDetails  ===  getInfo == Error => ".$e->getMessage());
            Log::error("==== Reports ==  GetUserDetails  ===  getInfo == Error Line No => ".$e->getLine());
            throw $e;
        }
    }
}
