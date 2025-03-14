<?php


namespace App\Http\Services\V1;

use Illuminate\Support\Facades\Log;

class CurlService
{
    /**
     * @method callCurl()
     *
     * @param array $data                   Curl input data
     * @param string $data.url           URL which want to hit in Curl Request
     * @param array $data.post_fields    POST data to send in Curl Request
     * @param array $data.headers        Header passed with Curl Request
     */
    public function callCurl(array $data)
    {
        // echo json_encode($data);die;
        if (!empty($data) && is_array($data)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $data['url']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // Post Fields
            if (!empty($data['post_fields'])) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data['post_fields']));
            }
            // Headers
            if (!empty($data['headers'])) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $data['headers']);
            }

            $response = curl_exec($ch);
            $error    = curl_error($ch);
            $errno    = curl_errno($ch);
            // Log::info("== CurlService::handler - Response => " . $response);
            if ($errno) {
                curl_close($ch);
                Log::error("== CurlService::handler - Error => " . $error);
                // throw new \RuntimeException($error, $errno);
                return false;
            }
            curl_close($ch);
            return $response;
        }
        return false;
    }
}
