<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiInvalidArgumentException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\SocialLoginRequest;
use App\Managers\AuthManager;
use App\Services\AuthApiService;
use Illuminate\Http\Request;

class AuthApiController extends Controller
{
    private $service;

    public function __construct(AuthApiService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function userLoginPost(LoginRequest $loginRequest)
    {
        return $this->jsonResponse(
            $loginRequest,
            $this->service->userLoginPost($loginRequest),
            $this->getDefaultCacheTime(), 200
        );
    }

    public function userAuthCredentialKeyGet(Request $request)
    {
        return $this->jsonResponse(
            $request,
            [
                'code' => 200,
                'isSuccess' => true,
                'message' => 'Credential key created',
                'responseBody' => AuthManager::createCredentialKey(),
            ],
            $this->getDefaultCacheTime(), 200
        );
    }

    public function userLogoutPost(Request $request)
    {
        return $this->jsonResponse(
            $request,
            $this->service->userLogoutPost($request),
            $this->getDefaultCacheTime(), 200
        );
    }

    public function userLogoutAllPost(Request $request)
    {
        return $this->jsonResponse(
            $request,
            $this->service->userLogoutAllPost($request),
            $this->getDefaultCacheTime(), 200
        );
    }

    public function userRegistrationPost(RegistrationRequest $registrationRequest)
    {
        return $this->jsonResponse(
            $registrationRequest,
            $this->service->userRegistrationPost($registrationRequest),
            $this->getDefaultCacheTime(), 200
        );
    }

    public function userSocialLoginPost(SocialLoginRequest $socialLoginRequest)
    {
        return $this->jsonResponse(
            $socialLoginRequest,
            $this->service->userSocialLoginPost($socialLoginRequest),
            $this->getDefaultCacheTime(), 200
        );
    }

    private function convertToBoolean($value, string $fieldName)
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            $lowercaseValue = strtolower(trim($value));
            if ($lowercaseValue === 'true' || $lowercaseValue === '1') {
                return true;
            } elseif ($lowercaseValue === 'false' || $lowercaseValue === '0') {
                return false;
            }
        }

        if (is_numeric($value)) {
            if ($value == 1) {
                return true;
            } elseif ($value == 0) {
                return false;
            }
        }

        throw new ApiInvalidArgumentException("The value of {$fieldName} must be a boolean (true/false, 1/0, \"true\"/\"false\")");
    }
}
