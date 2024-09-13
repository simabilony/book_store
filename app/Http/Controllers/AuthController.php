<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Service\AuthService;
use Illuminate\Http\Request;
class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->authService = $authService;
    }
    public function login(AuthRequest $request)
    {
        return $this->authService->login($request);
    }
    public function register(AuthRequest $request)
    {
        return $this->authService->register($request);
    }
    public function logout()
    {
        return $this->authService->logout();
    }
    public function refresh()
    {
        return $this->authService->refresh();
    }
}
