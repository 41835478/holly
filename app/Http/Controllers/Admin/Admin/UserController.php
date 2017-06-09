<?php

namespace App\Http\Controllers\Admin\Admin;

use App\DataTables\AdminUserDataTable;
use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function users(AdminUserDataTable $dataTable)
    {
        return $dataTable->render('admin.admin.users');
    }

    public function showCreateUser()
    {
        return view('admin.admin.create-user');
    }

    public function createUser(Request $request)
    {
        $this->validate($request, [
            'email' => 'bail|required|email|max:100|unique:admin_users',
            'username' => 'required|max:12',
            'password' => 'required',
        ]);

        $user = AdminUser::createUser($request->input());

        return api("管理员 <strong>{$user->username}</strong> 创建成功!");
    }

    public function deleteUser(AdminUser $user)
    {
        $user->delete();

        return api("管理员 <strong>{$user->username}</strong> 删除成功!");
    }
}
