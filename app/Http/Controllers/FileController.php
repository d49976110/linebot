<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\File;
use App\Repositories\FileRepository;


class FileController extends Controller
{
    public function create()
    {
        return view('file');
    }

    public function store(Request $request, FileRepository $fileRepository)
    {
        $fileRepository->store($request);

        return "上傳成功";
    }
}
