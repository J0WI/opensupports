<?php
use Respect\Validation\Validator as DataValidator;

/**
 * @api {post} /system/add-api-key Add APIKey
 * @apiVersion 4.8.0
 *
 * @apiName Add APIKey
 *
 * @apiGroup System
 *
 * @apiDescription This path creates a new APIKey.
 *
 * @apiPermission staff3
 *
 * @apiParam {String} name Name of the new APIKey.
 * @apiParam {Boolean} canCreateUsers canCreateUsers determinates if the apikey has the permission to create users 
 * @apiParam {Boolean} canCreateTickets canCreateTickets determinates if the apikey has the permission to create tickets
 * @apiParam {Boolean} canCommentTickets canCommentTickets determinates if the apikey has the permission to comment tickets
 * @apiParam {Boolean} shouldReturnTicketNumber shouldReturnTicketNumber determinates if the apikey has the permission of returning ticket number after ticket creation
 * @apiUse NO_PERMISSION
 * @apiUse INVALID_NAME
 * @apiUse NAME_ALREADY_USED
 *
 * @apiSuccess {String} data Token of the APIKey.
 *
 */

class AddAPIKeyController extends Controller {
    const PATH = '/add-api-key';
    const METHOD = 'POST';

    public function validations() {
        return [
            'permission' => 'staff_3',
            'requestData' => [
                'name' => [
                    'validation' => DataValidator::notBlank()->length(2, 55)->alnum(),
                    'error' => ERRORS::INVALID_NAME
                ]
            ]
        ];
    }

    public function handler() {
        $apiInstance = new APIKey();

        $name = Controller::request('name');
        $canCreateUsers = (bool)Controller::request('canCreateUsers');
        $canCreateTickets = (bool)Controller::request('canCreateTickets');
        $canCommentTickets = (bool)Controller::request('canCommentTickets');
        $shouldReturnTicketNumber = (bool)Controller::request('shouldReturnTicketNumber');

        $keyInstance = APIKey::getDataStore($name, 'name');

        if($keyInstance->isNull()){
            $token = Hashing::generateRandomToken();

            $apiInstance->setProperties([
                'name' => $name,
                'token' => $token,
                'canCreateUsers' => $canCreateUsers,
                'canCreateTickets' => $canCreateTickets,
                'canCommentTickets' => $canCommentTickets,
                'shouldReturnTicketNumber' => $shouldReturnTicketNumber
            ]);

            $apiInstance->store();
            Response::respondSuccess($token);
        } else {
            throw new RequestException(ERRORS::NAME_ALREADY_USED);
        }

    }
}