<?php

namespace App\Services\AuthService;

use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Exceptions\CustomException;
use App\Models\User;


class DoLogout extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        if (auth()->check()) {
            auth()->user()->token()->revoke();
            $this->results['message'] = "Successfully logged out";
        } else {
            throw new CustomException('Unauthorized request', 401);
        }
    }
}
