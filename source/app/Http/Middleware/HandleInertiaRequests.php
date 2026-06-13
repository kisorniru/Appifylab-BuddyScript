<?php

namespace App\Http\Middleware;

use App\Constants\ImageDefaults;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => fn () => $request->user() ? [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'image' => $this->getUserProfilePhoto($request),
            ] : null,
        ];
    }

    private function getUserProfilePhoto(Request $request)
    {
        if (! $request->user() || ($request->user() && $request->user()->is_admin)) {
            return asset('images/avatar.svg');
        }

        return $request->user()->profile_picture ?: asset(ImageDefaults::PROFILE['default']);
    }

    public function handle(Request $request, $next)
    {
        if ($timezone = $request->header('X-Timezone')) {
            session(['timezone' => $timezone]);
        }

        return parent::handle($request, $next);
    }
}
