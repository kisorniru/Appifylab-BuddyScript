<?php

namespace App\Managers;

use App\Exceptions\ApiInvalidArgumentException;
use App\Models\LoginError;
use App\Models\LoginHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AuthManager
{
    private const CREDENTIAL_KEY_CACHE_PREFIX = 'auth_credential_key:';

    private const CREDENTIAL_KEY_EXPIRES_SECONDS = 60;

    public static function saveHistoryLog($userId, $ipAddress)
    {
        $loginHistoryEntity = new LoginHistory;
        $loginHistoryEntity->user_id = $userId;
        $loginHistoryEntity->ip_address = $ipAddress;
        $loginHistoryEntity->created_at = Carbon::now();
        $loginHistoryEntity->updated_at = Carbon::now();
        $loginHistoryEntity->save();

    }

    public static function saveErrorLog($userId, $ipAddress)
    {
        $loginErrorEntity = new LoginError;
        $loginErrorEntity->user_id = $userId;
        $loginErrorEntity->ip_address = $ipAddress;
        $loginErrorEntity->created_at = Carbon::now();
        $loginErrorEntity->updated_at = Carbon::now();
        $loginErrorEntity->save();

    }

    public static function getHashedTokenFromHeader()
    {
        $authHeader = request()->header('Authorization');
        $loginToken = null;

        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $fullToken = substr($authHeader, 7);

            if (str_contains($fullToken, '|')) {
                $tokenParts = explode('|', $fullToken, 2);
                $plainToken = $tokenParts[1];

                $loginToken = hash('sha256', $plainToken);
            } else {
                $loginToken = hash('sha256', $fullToken);
            }
        }

        return $loginToken;
    }

    public static function isUserRegistered(string $email): bool
    {
        $user = User::where('email', $email)
            ->whereNotNull('email_verified_at')
            ->first();

        return $user ? true : false;
    }

    public static function createCredentialKey(): array
    {
        $keyPair = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        if (! $keyPair || ! openssl_pkey_export($keyPair, $privateKey)) {
            throw new ApiInvalidArgumentException('Unable to create credential key.');
        }

        $details = openssl_pkey_get_details($keyPair);
        $publicKey = $details['key'] ?? null;

        if (! $publicKey) {
            throw new ApiInvalidArgumentException('Unable to create credential key.');
        }

        $credentialKeyId = (string) Str::uuid();

        Cache::put(
            self::credentialKeyCacheKey($credentialKeyId),
            $privateKey,
            self::CREDENTIAL_KEY_EXPIRES_SECONDS
        );

        return [
            'credentialKeyId' => $credentialKeyId,
            'publicKey' => $publicKey,
            'expiresIn' => self::CREDENTIAL_KEY_EXPIRES_SECONDS,
        ];
    }

    public static function decryptCredentialKey(?string $credentialKeyId, array $encryptedValues): array
    {
        if (! $credentialKeyId) {
            throw new ApiInvalidArgumentException('Credential key is required.');
        }

        $privateKey = Cache::pull(self::credentialKeyCacheKey($credentialKeyId));

        if (! $privateKey) {
            throw new ApiInvalidArgumentException('Credential key is invalid or expired.');
        }

        $decryptedValues = [];
        foreach ($encryptedValues as $field => $encryptedValue) {
            $cipherText = base64_decode((string) $encryptedValue, true);

            if (! $cipherText || ! openssl_private_decrypt($cipherText, $plainText, $privateKey, OPENSSL_PKCS1_OAEP_PADDING)) {
                throw new ApiInvalidArgumentException("The value of {$field} could not be decrypted.");
            }

            $decryptedValues[$field] = $plainText;
        }

        return $decryptedValues;
    }

    private static function credentialKeyCacheKey(string $credentialKeyId): string
    {
        return self::CREDENTIAL_KEY_CACHE_PREFIX.$credentialKeyId;
    }
}
