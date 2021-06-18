<?php

namespace App\Repositories;

use Yish\Generators\Foundation\Repository\Repository;
use App\User;

class UserRepository
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }


    public function store($request)
    {
        $this->user->create([
            'name' => $request->name,
            'user_id' => $request->userId,
        ]);
    }

    public function update(User $user)
    {
        $user->update([
            'isVerified' => 1,
        ]);
    }
}
