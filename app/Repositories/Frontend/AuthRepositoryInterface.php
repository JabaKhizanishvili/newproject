<?php

namespace App\Repositories\Frontend;

use App\Http\Request\LoginFrontRequest;
use App\Http\Request\LoginRequest;

use App\Http\Request\RegisterRequest;
use Illuminate\Http\Request;

interface AuthRepositoryInterface
{

    public function view();

    public function login(LoginFrontRequest $request);

    public function register(string $locale, RegisterRequest $request);
}
