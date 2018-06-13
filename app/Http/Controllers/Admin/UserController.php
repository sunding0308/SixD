<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Mockery\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.pages.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ],$this->messages());

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        session()->flash('success', '管理员创建成功.');

        return redirect()->route('admin.user.index');
    }

    private function messages()
    {
        return [
            'name.required' => '用户名为必填项',
            'name.unique' => '用户名已存在',
            'email.required' => '邮箱为必填项',
            'email.unique' => '邮箱已存在',
            'email.email' => '邮箱格式不正确',
            'password.required' => '密码为必填项',
            'password.min' => '密码至少为6位',
            'password.confirmed' => '两次输入的密码不一致',
            'password_confirmation.required' => '确认密码为必填项',
        ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.pages.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User                     $user
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ],$this->messages());

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        session()->flash('success', '管理员更新成功.');

        return redirect()->route('admin.user.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // delete
        $user->delete();

        // redirect
        Session::flash('success', '管理员删除成功.');

        return redirect()->route('admin.user.index');
    }
}
