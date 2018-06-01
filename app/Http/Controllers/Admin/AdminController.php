<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminUser;
use App\Http\Controllers\AdminBaseController;
//use App\Models\User;
//use Illuminate\Http\Request;
use App\Repositories\UserRepository;

class AdminController extends AdminBaseController {

    protected $userRepository;

 

//    protected $userRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository) {
        parent::__construct();

        $this->middleware('auth.admin', [
            'except' => [
                'createAccount',
            ]
        ]);
        $this->userRepository = $userRepository;

    }

    /**
     * Creates a new admin account.
     *
     * @return void
     */
    public function createAccount() {
        if (AdminUser::all()->isNotEmpty()) {
            echo 'Admin account is already setup.';
            return;
        }

        AdminUser::create([
            'first_name' => 'Todd',
            'last_name' => 'Farrell',
            'username' => 'todd',
            'email' => 'todd.farrell@hellolayover.com',
            'password' => bcrypt('Todd@HelloLayover'),
        ]);

        echo 'Admin account created successfully.';
    }

    public function dashboard() {
        return view('admin.dashboard', [
            'activeMenu' => 'dashboard',
            'today' => $this->today,
           // 'users' => $this->userRepository->getAllUser(),
       
           
        ]);
    }

}
