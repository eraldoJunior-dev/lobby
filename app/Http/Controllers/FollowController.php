<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    /**
     * Segue/deixa de seguir outro usuário.
     */
    public function store(Request $request, User $user): JsonResponse|RedirectResponse
    {
        $me = $request->user();

        abort_if($user->is($me), 422, __('Você não pode seguir a si mesmo.'));

        $changes = $me->following()->toggle($user->id);

        if (count($changes['attached']) > 0) { \App\Support\LobbyNotifications::send($user, $me, 'follow', $me->name.' começou a seguir você.', route('profile.show', ['user' => $me->username], false)); }

        if ($request->wantsJson()) {
            return response()->json(['following' => count($changes['attached']) > 0]);
        }

        return redirect()->back();
    }
}
