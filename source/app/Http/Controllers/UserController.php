<?php

namespace App\Http\Controllers;

use App\Helpers\DateFormat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $offset = $request->get('offset', 10);

        $users = User::select('id', 'name', 'account_status as status',
            DB::raw("REGEXP_REPLACE(email, '_deleted_.*$', '') AS email"),
            DB::raw('(CASE WHEN users.deleted_at IS NULL THEN 0 ELSE 1 END) AS "isDeleted"'),
            DB::raw('(CASE WHEN users.deleted_at IS NULL THEN NULL ELSE
                                    '.DateFormat::formatDbColumn('users.deleted_at', 'Mon dd, YYYY at HH24:MI').'
                                END) AS "deletedAt"')
        )
            ->selectImageUrl()
            ->where('is_admin', false)
            ->search()
            ->activeInactiveDeleted()
            ->orderBy('id', 'DESC')
            ->paginate($offset);

        return Inertia::render('Admin/Users/List', [
            'users' => $users,
        ]);
    }

    public function userIdActiveStatus(Request $request, User $user)
    {
        try {
            DB::beginTransaction();

            $user->update([
                'account_status' => ! empty($request->status),
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }

        return redirect()->back()->with('success', 'User status updated.');
    }
}
