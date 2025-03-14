<?php


namespace App\Http\Services\V1;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendMailService
{
    public function mailSend($data = array(),$template = null)
    {
        try {
            if(empty($data) || empty($template)){ return false; }
            $name = $data['user_name'];
            $email = $data['user_email'];
            $subject = $data['email_subject'];
            $email_from = $data['email_from'];
            $email_from_name = $data['email_from_name'];
            $message = $data['email_msg'];
            $attachments = !empty($data['attachments']) ? $data['attachments'] : array();
            $cc_email = !empty($data['cc_email']) ? $data['cc_email'] : array();
            $type = !empty($data['type']) ? $data['type'] : '';
            // Log::info("template DATA => ".json_encode($data));
            return Mail::send(['html'=>$template], $data, function($message) use($email,$name,$subject,$email_from,$email_from_name,$attachments,$cc_email,$type) {
                $message->to($email, $name)->subject
                    ($subject);
                if(!empty($attachments)){
                    foreach ($attachments as $file_path) {
                        $message->attach($file_path);
                    }
                }
                if(!empty($cc_email) && $type == 'report'){
                    foreach ($cc_email as $email) {
                        $message->cc($email);
                    }
                }
                // $message->bcc('rizwanahmad.momin@upsidelms.com','Rizwan Ahmad');
                $message->from($email_from,$email_from_name);
            });
            //echo "Basic Email Sent. Check your inbox.";
        } catch (\Exception $e) {
            Log::info("=== SendMailService  ===  sendMail === Error = ".$e->getMessage());
            // return false;
            throw ($e);

        }
    }
}
