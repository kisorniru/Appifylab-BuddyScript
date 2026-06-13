<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\SocialLoginRequest;
use Illuminate\Http\Request;

interface AuthApiRepositoryInterface
{
    public function findById(string $modelClass, int $id);

    public function findAll(string $modelClass, array $conditions = [], array $orderBy = [], ?int $limit = null, ?int $offset = null): array;

    public function findBy(string $modelClass, array $conditions, array $orderBy = [], ?int $limit = null, ?int $offset = null): array;

    public function findOneBy(string $modelClass, array $conditions);

    public function create(string $modelClass, array $data);

    public function update(string $modelClass, int $id, array $data): bool;

    public function delete(string $modelClass, array $conditions): bool;

    public function count(string $modelClass, array $conditions = []): int;

    public function exists(string $modelClass, array $conditions): bool;

    public function userLoginPost(LoginRequest $loginRequest): mixed;

    public function userLogoutPost(Request $request): mixed;

    public function userLogoutAllPost(Request $request): mixed;

    public function userRegistrationPost(RegistrationRequest $registrationRequest): mixed;

    public function userSocialLoginPost(SocialLoginRequest $socialLoginRequest): mixed;
}
