<?php

namespace CustomValidations;

use Respect\Validation\Rules\AbstractRule;

class Captcha extends AbstractRule {
    private $dataStoreName;
    
    public function __construct($apiKeyPermissionType = '') {
        if (in_array($apiKeyPermissionType, \APIKey::TYPES)) {
            $this->apiKeyType = $apiKeyPermissionType;
        } else if($apiKeyPermissionType) {
            throw new \Exception(\ERRORS::INVALID_API_KEY_TYPE);
        }
    }
    
    public function validate($reCaptchaResponse) {
        $reCaptchaPrivateKey = \Setting::getSetting('recaptcha-private')->getValue();
        $apiKey = \APIKey::getDataStore(\Controller::request('apiKey'), 'token');

        if (!$reCaptchaPrivateKey) return true;

        if (!$apiKey->isNull()){
            switch ($apiKeyPermissionType) {
                case 'TICKET_CREATE_PERMISSION':
                    return $apiKey->canCreateUsers;
                case 'USER_CREATE_PERMISSION' : 
                    return $apiKey->canCommentTickets;
                case 'TICKET_COMMENT_PERMISSION':
                    return $apiKey->canCreateTickets;
            }
        }

        $reCaptcha = new \ReCaptcha\ReCaptcha($reCaptchaPrivateKey);
        $reCaptchaValidation = $reCaptcha->verify($reCaptchaResponse, $_SERVER['REMOTE_ADDR']);

        return $reCaptchaValidation->isSuccess();
    }
}