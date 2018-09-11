<?php

namespace App\Http\Controllers\Admin;

use App\Version;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VersionController extends Controller
{
    public function index()
    {
        $versions = Version::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.pages.version.index', compact('versions'));
    }

    public function create()
    {
        return view('admin.pages.version.create');
    }

    public function store(Request $request)
    {
        // dd($request->file('file'));
        $validator = Validator::make($request->all(), [
            'version_name' => 'required|unique:versions',
            'file' => 'required',
        ],$this->messages());

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $versionCode = preg_replace("/[^0-9]/", '', $request->version_name);
        $latestVersionCode = optional(Version::latest()->first())->version_code;

        if ($latestVersionCode) {
            $validator->after(function ($validator) use ($versionCode, $latestVersionCode){
                if ($versionCode < $latestVersionCode) {
                    $validator->errors()->add('version_name', '版本号小于当前最新版本号');
                }
            });
            
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            }
        }

        $base_path = 'versions/' . $request->version_name . '/'; //存放目录
        Storage::disk('public')->putFileAs($base_path, $request->file('file'), $request->file('file')->getClientOriginalName());

        $user = Version::create([
            'version_name' => $request->version_name,
            'version_code' => $versionCode,
            'description' => $request->description,
            'url' => 'public/versions/' . $request->version_name . '/' . $request->file('file')->getClientOriginalName(),
        ]);

        session()->flash('success', '新版本添加成功.');

        return redirect()->route('admin.version.index');
    }

    private function messages()
    {
        return [
            'version_name.required' => '版本号为必填项',
            'version_name.unique' => '版本号已存在',
            'file.required' => '文件为必填项',
        ];
    }

    public function download(Request $request)
    {
        return Storage::download($request->url);
    }
}
