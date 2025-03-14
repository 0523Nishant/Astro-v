<?php


namespace App\Http\Services\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ValidateInputService
{
    public function handle(Request $request)
    {
        $rules = [
            "user_email" => "required|email|max:255",
            "first_name" => "required|max:30|regex:/^[a-zA-Z0-9 ']+$/",
            "last_name" => "required|max:30|regex:/^[a-zA-Z0-9 ']+$/",
            "mobile" => 'required|numeric|digits:10',
            "gender" => 'required',
            "birth_date" => 'required|date_format:Y-m-d|date',
            "father_name" => "required|max:30|regex:/^[a-zA-Z0-9 ']+$/",
            "father_occupation" => "required|max:30|regex:/^[a-zA-Z0-9 ']+$/",
            "mother_occupation" => "required|max:30|regex:/^[a-zA-Z0-9 ']+$/",
            "zone" => "required",
            "state" => "required",
            "district" => 'required',
            "sub_division" => 'required',
            "city" => 'required',
            "address" => "required|max:450|regex:/(^[-0-9A-Za-z.\\\,\/ \'&#@%]+$)/",
            "qualification" => 'required',
            "edu_status" => 'required',
            "emp_status" => 'required',
            "emp_status_value" => 'required',
            "college_name" => "required|max:450|regex:/^[a-zA-Z0-9 ']+$/",
            "work_with_flipkart" => "required",
            "internship_with_flipkart" => "required",
            "internship" => "required",
            "internship_prog" => "required",
            "g-recaptcha-response" => ['required', new \App\Rules\ValidateRecaptcha()],
            "email_otp" => "required|numeric|digits:6"
        ];
        $messages = [
            "user_email.required" => 'This field is required',
            "user_email.email" => 'Email should be valid email',
            "user_email.max" => 'Length should under 255 character',
            "first_name.required" => 'This field is required',
            "first_name.max" => 'Your input should not exceed 30 characters',
            "first_name.regex" => 'Use only alphanumeric characters.',
            "last_name.required" => 'This field is required',
            "last_name.max" => 'Your input should not exceed 30 characters',
            "last_name.regex" => 'Use only alphanumeric characters.',
            "mobile.required" => 'This field is required',
            "mobile.numeric" => 'This field should have Numbers',
            "mobile.digits" => 'Enter a valid mobile number',
            "gender.required" => 'This field is required',
            "birth_date.required" => 'This field is required',
            "birth_date.date_format" => 'The date format is invlaid',
            "birth_date.date" => 'Should be a valid date',
            "father_name.required" => 'This field is required',
            "father_name.max" => 'Your input should not exceed 30 characters',
            "father_name.regex" => 'Use only alphanumeric characters.',
            "father_occupation.required" => 'This field is required',
            "father_occupation.max" => 'Your input should not exceed 255 characters',
            "father_occupation.regex" => 'Use only alphanumeric characters.',
            "mother_occupation.required" => 'This field is required',
            "mother_occupation.max" => 'Your input should not exceed 255 characters',
            "mother_occupation.regex" => 'Use only alphanumeric characters.',
            "zone.required" => "This field is required",
            "state.required" => 'This field is requried',
            "district.required" => 'This field is required',
            "sub_division.required" => 'This field is required',
            "city.required" => 'This field is required',
            "address.required" => 'This field is required',
            "address.max" => 'Your input should not exceed 450 characters',
            "address.regex" => 'Special characters are not allowed',
            "qualification.required" => 'This field is required',
            "edu_status.required" => 'This field is required',
            "emp_status.required" => 'This field is required',
            "emp_status_value.required" => 'This field is required',
            "college_name.required" => 'This field is required',
            "college_name.max" => 'Your input should not exceed 450 characters',
            "college_name.regex" => 'Use only alphanumeric characters.',
            "work_with_flipkart.required" => 'This field is required',
            "internship_with_flipkart.required" => 'This field is required',
            "internship.required" => 'This field is required',
            "internship_prog.required" => 'This field is required',
            "g-recaptcha-response.required" => "Please verify You're not robot.",
            "email_otp.required" => "OTP is required",
            "email_otp.numeric" => "Only numbers are allowed",
            "email_otp.digits" => "Numbers should be in 6 digits"
        ];

        if($request->work_with_flipkart == 'yes'){
            $rules["location_name"] = "required|max:450|regex:/^[a-zA-Z0-9 ']+$/";
            $rules["location_from_date"] = "required|date";
            $rules["location_to_date"] = "required|date";

            $messages["location_name.required"] = "This field is required";
            $messages["location_name.max"] = "Your input should not exceed 450 characters";
            $messages["location_name.regex"] = "Use only alphanumeric characters.";

            $messages["location_from_date.required"] = "This field is required";
            $messages["location_from_date.date"] = "This should be a valid date";

            $messages["location_to_date.required"] = "This field is required";
            $messages["location_to_date.date"] = "This should be a valid date";
        }

        if($request->internship_with_flipkart == 'yes'){
            $rules["internship_name"] = "required|max:450|regex:/^[a-zA-Z0-9 ']+$/";
            $rules["internship_from_date"] = "required|date";
            $rules["internship_to_date"] = "required|date";

            $messages["internship_name.required"] = "This field is required";
            $messages["internship_name.max"] = "Your input should not exceed 450 characters";
            $messages["internship_name.regex"] = "Use only alphanumeric characters.";

            $messages["internship_from_date.required"] = "This field is required";
            $messages["internship_from_date.date"] = "This should be a valid date";

            $messages["internship_to_date.required"] = "This field is required";
            $messages["internship_to_date.date"] = "This should be a valid date";
        }

        if($request->internship_prog == 'yes'){
            $rules["ojt_location"] = "required";
            $messages["ojt_location.required"] = "This field is required";
        }

        if($request->register_for == 'delivery'){
            $rules["driving_license"] = "required";
            $rules["have_bike"] = "required";
            $rules["confirm_to_travel"] = "required";

            $messages["driving_license.required"] = "This field is required";
            $messages["have_bike.required"] = "This field is required";
            $messages["confirm_to_travel.required"] = "This field is required";
        }

        if($request->register_for == 'edab'){
            $rules["excel_knowledge"] = "required";
            $rules["special_ability"] = "required";

            $messages["excel_knowledge.required"] = "This field is required";
            $messages["special_ability.required"] = "This field is required";

            if($request->special_ability == 'Others'){
                $rules["special_ability_text"] = "required|max:450|regex:/^[a-zA-Z0-9 ']+$/";
                $messages["special_ability_text.required"] = "This field is required";
                $messages["special_ability_text.max"] = "Your input should not exceed 450 characters";
                $messages["special_ability_text.regex"] = "Use only alphanumeric characters.";
            }
        }

        return Validator::make($request->all(), $rules, $messages);
    }

    // public function validateCaptcha(Request $request){
    //     try {
    //         $captcha = Session::get('captcha');
    //         if (! Validator::make(Input::only('captcha'), ['captcha' => 'required|captcha'])->fails()) {
    //             Session::put('captcha', $captcha);
    //             return true;
    //         } else {
    //             return false;
    //         }
    //     } catch (\Exception $e) {
    //         Log::error("===  ValidateInputService  === validateCaptcha  ==== Error => ".$e->getMessage());
    //         return false;
    //     }
    // }
}
