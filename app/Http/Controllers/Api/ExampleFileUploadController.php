<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FileUpload;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\UserFirebase;
use Illuminate\Http\Request;

class ExampleFileUploadController extends Controller
{
    public function singleUpload(Request $request)
    {
        $user = $request->user();

        $request->validate(['file' => 'required|file']);

        $directory = FileUpload::directory(UserFirebase::class, $user);
        // $directory = FileUpload::directory('My Folder/MySubfolder/Foo', $user);

        $file = $request->file('file');
        $path = FileUpload::put($file, $directory);

        return ResponseFormatter::success(compact('path'), 'upload success');
    }

    public function multipleUpload(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'file' => 'required|array',
            'file.*' => 'required|file',
        ]);

        $paths = [];

        $directory = FileUpload::directory(UserFirebase::class, $user);
        // $directory = FileUpload::directory('My Folder/MySubfolder/Foo', $user);

        foreach (array_keys($request->file) as $i) {
            $file = $request->file("file.{$i}");
            $paths[] = FileUpload::put($file, $directory);
        }

        return ResponseFormatter::success(compact('paths'), 'upload success');
    }
}
