<?php

namespace App\Http\Services\V1;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PortalConfigService
{
    // protected $_conn    = 'lms_sqlsrv';
    protected $_errors  = [];

    /**
     * @method  validate()
     *  To get the clinet ID, Encryption key and IV key by portal Key
     *
     * @param string $portalId Client's Portal Key
     *
     * @return array return the array with "client_id", "encryption_key", and "iv_key" else false
     */
    public function validate($portalId = null)
    {
        if (!empty($portalId)) {
            $sql = "OPEN SYMMETRIC KEY UpsideKey DECRYPTION BY CERTIFICATE UpsideCert SELECT pk_clientid AS clientid, CONVERT(NVARCHAR(MAX), DecryptByKey(TX_APP_KEY)) as iv_key, TX_ACTIVE as isActive, TX_STATUS as status, NU_MAXUSERS as max_license, CONVERT(NVARCHAR(MAX), DecryptByKey(TX_RANDOM_ENCRYPTION_KEY)) as enc_key FROM CLIENTM WITH (NOLOCK) WHERE tx_clientKey=:portal_id AND (CONVERT(NVARCHAR(10),ISNULL(DT_LEARNERDATE,SYSDATETIMEOFFSET()),120) >= CONVERT(NVARCHAR(10),SYSDATETIMEOFFSET())) CLOSE SYMMETRIC KEY UpsideKey";

            //  AND TX_ACTIVE = 'Yes' AND TX_STATUS = 'All Access Enabled'
            // echo json_encode($result);exit;
            try {
                $result = DB::select(DB::raw($sql), ['portal_id' => $portalId]);
                if (!empty($result)) {
                    $result         = $result[0];
                    $clientId       = trim($result->clientid);
                    $ivKey          = trim($result->iv_key);
                    $encryptionKey  = trim($result->enc_key);
                    $isActive       = trim($result->isActive);
                    $status         = trim($result->status);
                    $maxLicense     = trim($result->max_license);

                    if (!empty($clientId)) {
                        if (!empty($isActive) && !empty($status) && ($isActive == 'Yes' && $status == 'All Access Enabled')) {
                            $encryptionOn = !empty($ivKey) && !empty($encryptionKey) ? true : false;
                            if (!$encryptionOn) {
                                Log::alert('==PortalConfigService::validate: Encryption and/or IV keys not found!');
                            }
                            return [
                                'client_id'         => $clientId,
                                'encryption_on'     => $encryptionOn,
                                'encryption_key'    => $encryptionKey,
                                'iv_key'            => $ivKey,
                                'max_license'       => $maxLicense
                            ];
                        } else {
                            if(!empty($isActive) && $isActive == 'Yes'){
                                array_push($this->_errors, 'Client is Inactive');
                            }
                            if(!empty($status) && $status == 'All Access Enabled'){
                                array_push($this->_errors, 'Access disabled');
                            }
                        }
                    } else {
                        Log::error('==PortalConfigService::validate: Invalid value received against parameter.(Portal Key)');
                        array_push($this->_errors, 'Invalid value received against parameter.(Portal Key)');
                    }
                } else {
                    array_push($this->_errors, 'Invalid value received against parameter.(Portal Key)');
                }
            } catch (\Exception $e) {
                array_push($this->_errors, 'Internal Server Error');
                Log::warning("==PortalConfigService::validate: Unable to execute Query - ");
                throw($e);
            }
        } else {
            array_push($this->_errors, 'Field is required. (Portal Key)');
        }
        return ['errors' => $this->_errors];
    }

    public function licenseStatus($clientId = null)
    {
        if ($clientId > 0) {
            $sql  = "SELECT (CASE WHEN NU_MAXUSERS IS NULL or NU_MAXUSERS >= (SELECT COUNT(PK_LEARNERID) FROM LEARNERM with(Nolock) WHERE FK_CLIENTID = cm.PK_CLIENTID AND TX_STATUS = 'active' AND TX_ACCOUNTTYPE NOT IN ('super_user')) THEN 1 ELSE 0 END) as licenseStatus
            FROM CLIENTM cm with(Nolock) WHERE cm.PK_CLIENTID =:clientId";
            try {
                $result = DB::select(DB::raw($sql), ['clientId' => $clientId]);
                return ($result[0]->licenseStatus == 1) ? TRUE : FALSE;
            } catch (\Exception $e) {
                Log::warning("==PortalConfigService::licenseStatus: Unable to execute Query - " . $e->getMessage());

            }
        }
        return false;
    }
}
