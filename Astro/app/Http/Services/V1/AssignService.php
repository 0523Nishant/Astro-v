<?php


namespace App\Http\Services\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class AssignService
{
    public function assignCurr(Request $request)
    {
        try {
            $portal = '';
            $token = '';
            $assign_arr = array(
                "content_type" => "curriculum",
                "users" => array(
                    "type" => "email",
                    "ids" => array(
                        array(
                            "content_id" => "CUR001",
                            "user_ids" => array($request->user_email)
                        ),
                        array(
                            "content_id" => "CUR002",
                            "user_ids" => array($request->user_email)
                        ),
                        array(
                            "content_id" => "CUR003",
                            "user_ids" => array($request->user_email)
                        ),
                    ),
                ),
            );
        } catch (\Exception $e) {
            Log::error("===  AssignService  ===  assignCurr  === Error => ".$e);
            throw $e;
        }
    }
}
