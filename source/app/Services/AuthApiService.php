<?php

namespace App\Services;

use App\Constants\AccountStatus;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\SocialLoginRequest;
use App\Http\Responses\LoginResponse;
use App\Http\Responses\SocialLoginResponse;
use App\Http\Responses\SocialLoginResponseBody;
use App\Http\Responses\SuccessResult;
use App\Managers\AuthManager;
use App\Managers\IpAddressManager;
use App\Managers\PasswordManager;
use App\Models\User;
use App\Repositories\Contracts\AuthApiRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthApiService
{
    protected $authApiRepository;

    public function __construct(AuthApiRepositoryInterface $authApiRepository)
    {
        $this->authApiRepository = $authApiRepository;
    }

    public function userLoginPost(LoginRequest $loginRequest): LoginResponse
    {
        DB::beginTransaction();

        try {
            $ipAddress = IpAddressManager::getIpAddress();
            $password = $this->resolveLoginPassword($loginRequest);

            $user = $this->authApiRepository->findOneBy(User::class, ['email' => $loginRequest->email]);

            if (! $user) {
                throw new \App\Exceptions\ApiUserNotFoundException;
            }

            if (! Hash::check($password, $user->password)) {
                AuthManager::saveErrorLog(
                    $user->id,
                    $ipAddress
                );
                throw new \App\Exceptions\ApiInvalidCredentialException;
            }

            if ($user->account_status === AccountStatus::INACTIVE) {
                AuthManager::saveErrorLog(
                    $user->id,
                    $ipAddress
                );
                throw new \App\Exceptions\ApiUserAccountStatusInactiveException;
            }

            $repositoryResult = $this->authApiRepository->userLoginPost($loginRequest);

            $responseBody = new \App\Http\Responses\LoginResponseBody;
            $responseBody->id = $repositoryResult['user']->id;
            $responseBody->name = $repositoryResult['user']->name;
            $responseBody->email = $repositoryResult['user']->email;
            $responseBody->firstTimeLogin = $repositoryResult['firstTimeLogin'];
            $responseBody->apiToken = $repositoryResult['token'];

            $result = new LoginResponse;
            $result->code = 200;
            $result->isSuccess = true;
            $result->message = 'Login successful';
            $result->responseBody = $responseBody;

            DB::commit();

            return $result;

        } catch (\Exception $ex) {
            DB::rollBack();

            if (isset($user->id) && $user->id != null) {
                AuthManager::saveErrorLog(
                    $user->id,
                    $ipAddress
                );
            }

            throw $ex;
        }
    }

    public function userLogoutPost(Request $request): SuccessResult
    {
        DB::beginTransaction();

        try {
            $user = $request->user();

            if (! $user) {
                throw new \App\Exceptions\ApiNoTokenException;
            }

            $repositoryResult = $this->authApiRepository->userLogoutPost($request);

            $result = new SuccessResult;
            $result->code = 200;
            $result->isSuccess = true;
            $result->message = $repositoryResult['message'] ?? 'Successfully logged out';

            DB::commit();

            return $result;

        } catch (\Exception $ex) {
            DB::rollBack();

            throw $ex;
        }
    }

    public function userLogoutAllPost(Request $request): SuccessResult
    {
        DB::beginTransaction();

        try {
            $user = $request->user();

            if (! $user) {
                throw new \App\Exceptions\ApiNoTokenException;
            }

            $repositoryResult = $this->authApiRepository->userLogoutAllPost($request);

            $result = new SuccessResult;
            $result->code = 200;
            $result->isSuccess = true;
            $result->message = $repositoryResult['message'] ?? 'Successfully logged out from all devices';

            DB::commit();

            return $result;

        } catch (\Exception $ex) {
            DB::rollBack();

            throw $ex;
        }
    }

    public function userSocialLoginPost(SocialLoginRequest $socialLoginRequest): SocialLoginResponse
    {
        DB::beginTransaction();

        try {
            $ipAddress = IpAddressManager::getIpAddress();

            $user = $this->authApiRepository->findOneBy(User::class, ['email' => $socialLoginRequest->email]);

            if (! $user && ! empty($socialLoginRequest->phone)) {
                $user = $this->authApiRepository->findOneBy(User::class, ['provider_unique_id' => $socialLoginRequest->provider.'_'.$socialLoginRequest->provider_id]);
            }

            if ($user && $user->account_status === AccountStatus::INACTIVE) {
                AuthManager::saveErrorLog(
                    $user->id,
                    $ipAddress
                );
                throw new \App\Exceptions\ApiUserAccountStatusInactiveException;
            }

            $repositoryResult = $this->authApiRepository->userSocialLoginPost($socialLoginRequest);

            $responseBody = new SocialLoginResponseBody;
            $responseBody->id = $repositoryResult['user']->id;
            $responseBody->name = $repositoryResult['user']->name;
            $responseBody->email = $repositoryResult['user']->email;
            $responseBody->firstTimeLogin = $repositoryResult['firstTimeLogin'];
            $responseBody->apiToken = $repositoryResult['token'];

            $result = new SocialLoginResponse;
            $result->code = 200;
            $result->isSuccess = true;
            $result->message = 'Login successful';
            $result->responseBody = $responseBody;

            DB::commit();

            return $result;

        } catch (\Exception $ex) {
            DB::rollBack();

            throw $ex;
        }
    }

    public function userRegistrationPost(RegistrationRequest $registrationRequest): LoginResponse
    {
        DB::beginTransaction();

        try {
            $user = $this->authApiRepository->findOneBy(User::class, ['email' => $registrationRequest->email]);

            if ($user) {
                throw new \App\Exceptions\ApiUserAlreadyExistsException;
            }

            [$password, $passwordConfirm] = $this->resolveRegistrationPasswords($registrationRequest);

            PasswordManager::validatePassword(
                $password,
                $passwordConfirm
            );

            $registrationRequest->merge([
                'password' => $password,
                'passwordConfirm' => $passwordConfirm,
            ]);

            $repositoryResult = $this->authApiRepository->userRegistrationPost($registrationRequest);

            $responseBody = new \App\Http\Responses\LoginResponseBody;
            $responseBody->id = $repositoryResult['user']->id;
            $responseBody->name = $repositoryResult['user']->name;
            $responseBody->email = $repositoryResult['user']->email;
            $responseBody->firstTimeLogin = $repositoryResult['firstTimeLogin'];
            $responseBody->apiToken = $repositoryResult['token'];

            $result = new LoginResponse;
            $result->code = 200;
            $result->isSuccess = true;
            $result->message = 'Registration successful';
            $result->responseBody = $responseBody;

            DB::commit();

            return $result;

        } catch (\Exception $ex) {
            DB::rollBack();

            throw $ex;
        }
    }

    private function resolveLoginPassword(LoginRequest $loginRequest): string
    {
        $credentialKeyId = $loginRequest->input('credentialKeyId') ?: $loginRequest->input('challengeId');

        if ($credentialKeyId) {
            $credentials = AuthManager::decryptCredentialKey(
                $credentialKeyId,
                ['password' => $loginRequest->input('encryptedPassword') ?: $loginRequest->input('password')]
            );

            return $credentials['password'];
        }

        return (string) $loginRequest->input('password');
    }

    private function resolveRegistrationPasswords(RegistrationRequest $registrationRequest): array
    {
        $credentialKeyId = $registrationRequest->input('credentialKeyId') ?: $registrationRequest->input('challengeId');

        if ($credentialKeyId) {
            $credentials = AuthManager::decryptCredentialKey(
                $credentialKeyId,
                [
                    'password' => $registrationRequest->input('encryptedPassword') ?: $registrationRequest->input('password'),
                    'passwordConfirm' => $registrationRequest->input('encryptedPasswordConfirm') ?: $registrationRequest->input('passwordConfirm'),
                ]
            );

            return [
                $credentials['password'],
                $credentials['passwordConfirm'],
            ];
        }

        return [
            (string) $registrationRequest->input('password'),
            (string) $registrationRequest->input('passwordConfirm'),
        ];
    }
}
