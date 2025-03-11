<?php

namespace App\Services\UserInformation;

use App\Models\UserInformation;
use App\Rules\ExistsUuid;
use App\Services\DefaultService;
use App\Services\ServiceInterface;

class DeleteUserInformationService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto = $this->prepare($dto);
        $userinformation = UserInformation::find($dto['userinformation_id']);

        $this->results['message'] = "User successfully deleted";
        $this->results['data'] = $this->activeAndRemoveData($userinformation, $dto);
    }

    public function prepare($dto)
    {
        if (isset($dto['userinformation_uuid'])) {
            $dto['userinformation_id'] = $this->findIdByUuid(UserInformation::query(), $dto['userinformation_uuid']);
        }
        return $dto;
    }

    public function rules($dto)
    {
        return [
            'userinformation_uuid' => ['required', 'uuid', new ExistsUuid('user_informations')],
        ];
    }
}
