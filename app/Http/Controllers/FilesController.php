<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $file = $request->file('file');

        $current_user_id = Auth::user()->id;

        if(!Storage::directoryExists("{$current_user_id}")){
            Storage::makeDirectory("{$current_user_id}");
        }
        if(!Storage::directoryExists("{$current_user_id}/chunks")){
            Storage::makeDirectory("{$current_user_id}/chunks");
        }
        if(!Storage::directoryExists("{$current_user_id}/nfiles")){
            Storage::makeDirectory("{$current_user_id}/nfiles");
        }

        $path = Storage::path("{$current_user_id}/chunks/{$file->getClientOriginalName()}");

        File::append($path, $file->get());

        if ($request->has('is_last') && $request->boolean('is_last')) {
            $name = basename($path, '.part');

            File::move($path, Storage::path("{$current_user_id}/nfiles/{$name}"));
        }


        return response()->json(['uploaded' => true]);
    }

    public function download($name)
    {
        $current_user_id = Auth::user()->id;
        $path = Storage::path("{$current_user_id}/nfiles/{$name}");

        if (!File::exists($path)) {
            abort(404);
        }

        return response()->download($path, $name);
    }

    public function serve($name)
    {
        $current_user_id = Auth::user()->id;
        $path = Storage::path("{$current_user_id}/nfiles/{$name}");

        if (!File::exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }	

    public function destroy($name)
    {
        $current_user_id = Auth::user()->id;
        $path = Storage::path("{$current_user_id}/nfiles/{$name}");

        if (!File::exists($path)) {
            abort(404);
        }

        File::delete($path);

        return response()->json(['deleted' => true]);
    }

    public function destroyparts($name)
    {
        $current_user_id = Auth::user()->id;
        $path = Storage::path("{$current_user_id}/chunks/{$name}.part");

        if (!File::exists($path)) {
            abort(404);
        }

        File::delete($path);

        return response()->json(['deleted' => true]);
    }

    public function index()
    {

        $current_user_id = Auth::user()->id;

        $files = collect(Storage::files("{$current_user_id}/nfiles"))->map(function ($file) {
            return [
                'name' => basename($file),
                'size' => Storage::size($file),
                'url' => route('files.download', basename($file)),
                'url_file' => route('files.getit', basename($file)),
            ];
        });

        return response()->json($files);
    }

}