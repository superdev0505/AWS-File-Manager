<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\StorageService;
use Session;

class UserController extends BaseController
{
    private $storageService;

	/**
	 * ApiController constructor.
	 *
	 * @param StorageService $storageService
	 */
	public function __construct(StorageService $storageService)
	{

		$this->storageService = $storageService;

	}
    function index(Request $request) {
        return view('index.start');
    }
        
    function login(Request $request) {
        $database_path = env('DATABASE_PATH', "/");
        $username = $request->input('email');
        $password = $request->input('password');
        $result = $this->storageService->login($database_path, $username, $password);
        if ($result == false) {
            return back()->withErrors(['Email and Password are not matched.']);
        }
        else {
            $request->session()->put('username', $username);
            session()->put('username', $username);
            session()->put('role', $result);
            return redirect('/browser');
        }
    }

    function logout(Request $request) {
        $request->session()->forget('username');
        session()->forget('username');
        session()->forget('role');
        return redirect('/');
    }
    // use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
