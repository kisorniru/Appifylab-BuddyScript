<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function home()
    {
        $dashboardStats = DB::query()
            ->selectSub(User::selectRaw('count(*)')->where('is_admin', '!=', true), 'total_users')->first();

        $users = User::select(
            DB::raw('count(CASE WHEN created_at >= CURRENT_DATE THEN 1 END) as today'),
            DB::raw('count(CASE WHEN created_at >= ? THEN 1 END) as week'),
            DB::raw('count(CASE WHEN created_at >= ? THEN 1 END) as month'),
            DB::raw('count(CASE WHEN EXTRACT(YEAR FROM created_at) = EXTRACT(YEAR FROM CURRENT_DATE) THEN
                                1 END) as year')
        )
            ->setBindings([
                Carbon::now()->startOfWeek(),
                Carbon::now()->startOfMonth(),
            ])
            ->where('is_admin', false)
            ->first();

        return Inertia::render('Admin/Dashboard', [
            'cards' => [
                'users' => $dashboardStats->total_users ?: 0,
            ],
            'users' => [
                ['key' => 'today', 'value' => $users->today],
                ['key' => 'week', 'value' => $users->week],
                ['key' => 'month', 'value' => $users->month],
                ['key' => 'year', 'value' => $users->year],
            ],
        ]);
    }
}
