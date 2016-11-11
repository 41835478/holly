<?php

namespace App\Http\Controllers\Admin\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function show(Request $request, $user = null)
    {
        if (is_null($user)) {
            $user = $request->user();
        }

        $this->authorize('view', $user);

        return view('admin.admin.profile', compact('user'));
    }

    public function edit(Request $request, $user = null)
    {
        if (is_null($user)) {
            $user = $request->user();
        }

        $this->authorize('update', $user);

        $this->validate($request, [
            'username' => 'sometimes|max:12',
            'avatar' => 'sometimes|mimes:jpg,jpeg,png,gif',
        ]);

        if ($username = $request->input('username')) {
            $user->username = $username;
        }

        if ($password = $request->input('password')) {
            $user->password = bcrypt($password);
        }

        if ($request->input('use_default_avatar')) {
            $user->useDefaultAvatar();
        }

        if ($avatar = $request->file('avatar')) {
            $user->storeAvatarFile($avatar);
        }

        $user->save();

        return api(compact('user'))->message('修改成功!');
    }
}
