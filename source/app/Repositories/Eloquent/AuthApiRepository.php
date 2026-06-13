<?php

namespace App\Repositories\Eloquent;

use App\Constants\AccountStatus;
use App\Constants\SocialUser;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\SocialLoginRequest;
use App\Managers\AuthManager;
use App\Managers\IpAddressManager;
use App\Models\ActiveHistory;
use App\Models\User;
use App\Models\UserDevice;
use App\Repositories\Contracts\AuthApiRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthApiRepository implements AuthApiRepositoryInterface
{
    public function findById(string $modelClass, int $id)
    {
        if (! is_string($modelClass) || ! class_exists($modelClass)) {
            throw new \InvalidArgumentException('You must pass a valid model class name, e.g., User::class');
        }

        return $modelClass::find($id);
    }

    public function findAll(string $modelClass, array $conditions = [], array $orderBy = [], ?int $limit = null, ?int $offset = null): array
    {
        if (! is_string($modelClass) || ! class_exists($modelClass)) {
            throw new \InvalidArgumentException('You must pass a valid model class name, e.g., User::class');
        }
        $query = $modelClass::query();
        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }
        foreach ($orderBy as $field => $direction) {
            $query->orderBy($field, $direction);
        }
        if ($limit) {
            $query->limit($limit);
        }
        if ($offset) {
            $query->offset($offset);
        }

        return $query->get()->toArray();
    }

    public function findBy(string $modelClass, array $conditions, array $orderBy = [], ?int $limit = null, ?int $offset = null): array
    {
        if (! is_string($modelClass) || ! class_exists($modelClass)) {
            throw new \InvalidArgumentException('You must pass a valid model class name, e.g., User::class');
        }

        return $this->findAll($modelClass, $conditions, $orderBy, $limit, $offset);
    }

    public function findOneBy(string $modelClass, array $conditions)
    {
        if (! is_string($modelClass) || ! class_exists($modelClass)) {
            throw new \InvalidArgumentException('You must pass a valid model class name, e.g., User::class');
        }
        $query = $modelClass::query();
        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }

        return $query->first();
    }

    public function create(string $modelClass, array $data)
    {
        if (! is_string($modelClass) || ! class_exists($modelClass)) {
            throw new \InvalidArgumentException('You must pass a valid model class name, e.g., User::class');
        }

        return $modelClass::create($data);
    }

    public function update(string $modelClass, int $id, array $data): bool
    {
        if (! is_string($modelClass) || ! class_exists($modelClass)) {
            throw new \InvalidArgumentException('You must pass a valid model class name, e.g., User::class');
        }
        $model = $modelClass::find($id);
        if ($model) {
            return $model->update($data);
        }

        return false;
    }

    public function delete(string $modelClass, array $conditions): bool
    {
        if (! is_string($modelClass) || ! class_exists($modelClass)) {
            throw new \InvalidArgumentException('You must pass a valid model class name, e.g., User::class');
        }
        $query = $modelClass::query();
        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }

        return $query->delete() > 0;
    }

    public function count(string $modelClass, array $conditions = []): int
    {
        if (! is_string($modelClass) || ! class_exists($modelClass)) {
            throw new \InvalidArgumentException('You must pass a valid model class name, e.g., User::class');
        }
        $query = $modelClass::query();
        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }

        return $query->count();
    }

    public function exists(string $modelClass, array $conditions): bool
    {
        if (! is_string($modelClass) || ! class_exists($modelClass)) {
            throw new \InvalidArgumentException('You must pass a valid model class name, e.g., User::class');
        }

        return $this->count($modelClass, $conditions) > 0;
    }

    public function userLoginPost(LoginRequest $loginRequest): mixed
    {
        $ipAddress = IpAddressManager::getIpAddress();

        $user = $this->findOneBy(modelClass: User::class, conditions: ['email' => $loginRequest->email]);

        $firstTimeLogin = is_null($user->last_actived_at);

        $user->last_actived_at = Carbon::now();
        $user->save();

        $tokenName = request()->header('User-Agent') ?? 'unknown-device';
        $customPlainToken = hash('sha512', Str::uuid()->toString().microtime(true).Str::random(100));

        $accessToken = $user->tokens()->create([
            'name' => $tokenName,
            'token' => hash('sha256', $customPlainToken),
            'abilities' => ['*'],
            'expires_at' => config('sanctum.expiration') ? now()->addMinutes(config('sanctum.expiration')) : null,
        ]);

        $apiToken = $accessToken->id.'|'.$customPlainToken;

        $activeHistory = new ActiveHistory;
        $activeHistory->user_id = $user->id;
        $activeHistory->created_at = Carbon::now();
        $activeHistory->updated_at = Carbon::now();
        $activeHistory->save();

        AuthManager::saveHistoryLog(
            $user->id,
            $ipAddress
        );

        return [
            'user' => $user,
            'firstTimeLogin' => $firstTimeLogin,
            'token' => $apiToken,
        ];
    }

    public function userLogoutPost(Request $request): mixed
    {
        $user = request()->user();

        $loginToken = AuthManager::getHashedTokenFromHeader();
        UserDevice::where('token', $loginToken)
            ->delete();

        $currentToken = $user->currentAccessToken();
        if ($currentToken) {
            $currentToken->delete();
        }

        return [
            'success' => true,
            'message' => 'Successfully logged out',
        ];
    }

    public function userLogoutAllPost(Request $request): mixed
    {
        $user = request()->user();

        $loginToken = AuthManager::getHashedTokenFromHeader();
        UserDevice::where('token', $loginToken)
            ->delete();

        $user->tokens()->delete();

        return [
            'success' => true,
            'message' => 'Successfully logged out from all devices',
        ];
    }

    public function userSocialLoginPost(SocialLoginRequest $socialLoginRequest): mixed
    {
        $ipAddress = IpAddressManager::getIpAddress();

        $user = $this->findOneBy(User::class, ['email' => $socialLoginRequest->email]);

        $firstTimeLogin = ! $user || is_null($user->last_actived_at);

        if (! $user && ! empty($socialLoginRequest->phone)) {
            $user = $this->findOneBy(User::class, ['provider_unique_id' => $socialLoginRequest->provider.'_'.$socialLoginRequest->provider_id]);
        }

        if (! $user) {
            $user = User::create([
                'first_name' => $socialLoginRequest->firstName,
                'last_name' => $socialLoginRequest->lastName,
                'name' => trim($socialLoginRequest->firstName.' '.$socialLoginRequest->lastName),
                'email' => $socialLoginRequest->email,
                'phone' => $socialLoginRequest->phone,
                'account_status' => AccountStatus::ACTIVE,
                'is_social_user' => SocialUser::TRUE,
                'provider' => $socialLoginRequest->provider,
                'provider_id' => $socialLoginRequest->providerId,
                'provider_unique_id' => $socialLoginRequest->provider.'_'.$socialLoginRequest->providerId,
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make(Str::random(32)),
            ]);
        } else {
            if ($user->first_name !== $socialLoginRequest->firstName || $user->last_name !== $socialLoginRequest->lastName) {
                $user->update([
                    'first_name' => $socialLoginRequest->firstName,
                    'last_name' => $socialLoginRequest->lastName,
                    'name' => trim($socialLoginRequest->firstName.' '.$socialLoginRequest->lastName),
                ]);
            }
        }

        $user->last_actived_at = Carbon::now();
        $user->save();

        $tokenName = request()->header('User-Agent') ?? 'unknown-device';
        $customPlainToken = hash('sha512', Str::uuid()->toString().microtime(true).Str::random(100));

        $accessToken = $user->tokens()->create([
            'name' => $tokenName,
            'token' => hash('sha256', $customPlainToken),
            'abilities' => ['*'],
            'expires_at' => config('sanctum.expiration') ? now()->addMinutes(config('sanctum.expiration')) : null,
        ]);

        $apiToken = $accessToken->id.'|'.$customPlainToken;

        $activeHistory = new ActiveHistory;
        $activeHistory->user_id = $user->id;
        $activeHistory->created_at = Carbon::now();
        $activeHistory->updated_at = Carbon::now();
        $activeHistory->save();

        AuthManager::saveHistoryLog(
            $user->id,
            $ipAddress
        );

        return [
            'user' => $user,
            'firstTimeLogin' => $firstTimeLogin,
            'token' => $apiToken,
            'provider' => $socialLoginRequest->provider,
            'provider_id' => $socialLoginRequest->providerId,
            'provider_unique_id' => $socialLoginRequest->provider.'_'.$socialLoginRequest->providerId,
        ];
    }

    public function userRegistrationPost(RegistrationRequest $registrationRequest): mixed
    {
        $ipAddress = IpAddressManager::getIpAddress();

        $user = $this->findOneBy(User::class, ['email' => $registrationRequest->email]);

        $firstTimeLogin = ! $user || is_null($user->last_actived_at);

        $user = User::create([
            'first_name' => $registrationRequest->firstName,
            'last_name' => $registrationRequest->lastName,
            'name' => trim($registrationRequest->firstName.' '.$registrationRequest->lastName),
            'email' => $registrationRequest->email,
            'phone' => $registrationRequest->phone,
            'account_status' => AccountStatus::ACTIVE,
            'is_social_user' => SocialUser::FALSE,
            'account_status_updated_at' => Carbon::now(),
            'password' => Hash::make($registrationRequest->password),
        ]);

        $user->last_actived_at = Carbon::now();
        $user->save();

        $tokenName = request()->header('User-Agent') ?? 'unknown-device';
        $customPlainToken = hash('sha512', Str::uuid()->toString().microtime(true).Str::random(100));

        $accessToken = $user->tokens()->create([
            'name' => $tokenName,
            'token' => hash('sha256', $customPlainToken),
            'abilities' => ['*'],
            'expires_at' => config('sanctum.expiration') ? now()->addMinutes(config('sanctum.expiration')) : null,
        ]);

        $apiToken = $accessToken->id.'|'.$customPlainToken;

        $activeHistory = new ActiveHistory;
        $activeHistory->user_id = $user->id;
        $activeHistory->created_at = Carbon::now();
        $activeHistory->updated_at = Carbon::now();
        $activeHistory->save();

        AuthManager::saveHistoryLog(
            $user->id,
            $ipAddress
        );

        return [
            'user' => $user,
            'firstTimeLogin' => $firstTimeLogin,
            'token' => $apiToken,
        ];
    }
}
