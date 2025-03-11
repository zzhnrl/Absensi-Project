<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserInformation;
use App\Rules\ExistsUuid;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class RemoveUserService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto = $this->prepare($dto);

        DB::beginTransaction();
        try {
            $user = User::find($dto['user_id']);
            if (!$user) {
                throw new Exception("User not found.");
            }
            $this->activeAndRemoveData($user, $dto);

            $userInformation = UserInformation::where('user_id', $dto['user_id'])->first();
            if ($userInformation) {
                $this->activeAndRemoveData($userInformation, $dto);
            }

            DB::commit();

            $this->results['message'] = "User and associated information successfully deleted";
        } catch (Exception $e) {
            DB::rollBack();
            $this->results['message'] = "Failed to delete user and associated information";
            $this->results['error'] = $e->getMessage();
        }
    }

    public function prepare($dto)
    {
        if (isset($dto['user_uuid'])) {
            $dto['user_id'] = $this->findIdByUuid(User::query(), $dto['user_uuid']);
        }
        return $dto;
    }

    public function rules($dto)
    {
        return [
            'user_uuid' => ['required', 'uuid', new ExistsUuid('users')]
        ];
    }
}
