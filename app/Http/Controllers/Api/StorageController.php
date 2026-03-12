<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// app/Helper/FileUpload.php
class StorageController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $path)
    {
        // example path:
        // storage/app/file-upload/
        // ├── laporan/                                     ← Models\Laporan
        // │   ├── {user_id}/                                 ← file folder user_id
        // │   │   └── 1760948945_68f5f2d1680f0.png             ← <time>_<uniqid>.<ext>
        // │   └── dokumen/                                 ← Custom Folder 'dokumen'
        // │       ├── {user_id}/                             ← file folder user_id
        // │       │   └── 1760948945_68f5f2d1680f0.docx        ← <time>_<uniqid>.<ext>
        // └── avatar/                                      ← Models\Avatar
        //     ├── {user_id}/                                 ← file folder user_id
        //     │   └── 1760948945_68f5f2d1680f0.jpg             ← <time>_<uniqid>.<ext>

        $user = $request->user();

        $request->validate(['download' => 'nullable|in:0,1']);

        try {
            $fileUpload = FileUpload::parse($path);

            if (! $fileUpload->exists()) {
                throw new \Exception('File not exists');
            }

            if (! $fileUpload->authenticate($user)) {
                throw new \Exception('Not authorized to access this file');
            }

            if ($request->boolean('download')) {
                return response()->download($fileUpload->file());
            }

            return response()->file($fileUpload->file());
        } catch (\Throwable $th) {
            // abort(400, $th->getMessage()); // for test only
            abort(404, 'File tidak ditemukan');
        }
    }
}
