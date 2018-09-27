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
        $validator = Validator::make($request->all(), [
            'version_name' => 'required|unique:versions',
            'version_code' => 'required|unique:versions',
            'file' => 'required',
        ],$this->messages());

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $versionCode = $request->version_code;
        $latestVersionCode = optional(Version::latest()->first())->version_code;

        if ($latestVersionCode) {
            $validator->after(function ($validator) use ($versionCode, $latestVersionCode){
                if ($versionCode < $latestVersionCode) {
                    $validator->errors()->add('version_code', '版本代码小于当前最新版本代码:'.$latestVersionCode);
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
            'version_code.required' => '版本代码为必填项',
            'version_code.unique' => '版本代码已存在',
            'file.required' => '文件为必填项',
        ];
    }

    public function download(Request $request)
    {
        return Storage::download($request->url);
    }

    public function destroy(Version $version)
    {
        // delete
        $version->delete();
        Storage::deleteDirectory(pathinfo($version->url)['dirname']);

        // redirect
        session()->flash('success', '版本删除成功.');

        return redirect()->route('admin.version.index');
    }
}
