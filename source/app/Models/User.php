<?php

namespace App\Models;

use App\Constants\ImageDefaults;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'gender',
        'profile_picture',
        'is_admin',
        'is_social_user',
        'provider',
        'provider_id',
        'provider_unique_id',
        'account_status',
        'account_status_updated_at',
        'last_actived_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function devices()
    {
        $tokens = $this->tokens()->pluck('token')->all();
        if (empty($tokens)) {
            return collect();
        }

        return UserDevice::whereIn('token', $tokens)
            ->where('user_devices.device_token', 'not like', '%com.google.android%')
            ->get();
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function feedItems()
    {
        return $this->hasMany(FeedItem::class, 'viewer_id');
    }

    public function receivedNotifications()
    {
        return $this->hasMany(Notification::class, 'receiver_id');
    }

    public function sentNotifications()
    {
        return $this->hasMany(Notification::class, 'sender_id');
    }

    public function scopeSearch(Builder $builder)
    {
        $request = request();

        return $builder->when(! empty($request->search), function ($q) use ($request) {
            $q->where(function ($q) use ($request) {
                return $q->where('users.id', 'iLIKE', "{$request->search}%")
                    ->orWhere('users.name', 'iLIKE', "%{$request->search}%");
            });
        });
    }

    public function scopeActiveInactive(Builder $builder)
    {
        $status = request()->input('status');

        return $builder->when($status === 'active', function ($query) {
            return $query->where('users.account_status', 1);
        })->when($status === 'inactive', function ($query) {
            return $query->where('users.account_status', 0);
        });
    }

    public function scopeActiveInactiveDeleted(Builder $builder)
    {
        $status = request()->input('status');

        return $builder->when($status === 'deleted', function ($query) {
            return $query->onlyTrashed();
        }, function ($query) {
            return $query->activeInactive();
        });
    }

    public function scopeSelectImageUrl(Builder $builder, $alias = 'imageUrl')
    {
        $defaultImage = asset(ImageDefaults::PROFILE['default']);

        return $builder->selectRaw("CASE WHEN users.profile_picture IS NULL THEN
                                        '{$defaultImage}'
                                    ELSE 
                                        users.profile_picture
                                    END AS \"{$alias}\"");
    }

    public function deleteDeviceAndTokens()
    {
        $tokens = $this->tokens()->pluck('token');

        if ($tokens->count() > 0) {
            UserDevice::whereIn('token', $tokens)
                ->where('user_devices.device_token', 'not like', '%com.google.android%')
                ->delete();
        }

        $this->tokens()->delete();
    }
}
