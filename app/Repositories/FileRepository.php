<?php

namespace App\Repositories;

use Yish\Generators\Foundation\Repository\Repository;
use App\User;
use App\File;
use Illuminate\Support\Facades\Storage;

class FileRepository
{
    protected $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function store($request)
    {
        $user = User::where('user_id', $request->userId)->first();

        $path = Storage::putFile('public', $request->file);
        $path = Storage::url($path);

        $this->file->create([
            'user_id' => $user->id,
            'path' => $path,
        ]);
    }
}
