<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminBaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Session;
use Illuminate\Support\Facades\Redirect;
use DB;
use Carbon\Carbon;
use App\Services\UserService;
use App\Repositories\UserRepository;

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
// * @name   : UsersController
// * @Date   : 19-April-2018
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//


class UserController extends AdminBaseController {

    private $userService;
    private $UserRepository;

    public function __construct(UserService $userService, UserRepository $UserRepository) {
        parent::__construct();
        $this->userService = $userService;
        $this->UserRepository = $UserRepository;
        $this->middleware('auth.admin');
    }

    //*************************************************//
    // * @name   : index
    // * @todo   : Display list of users
    // * @params :  
    // * @Date   : 19-April-2018
    //************************************************//

    public function index() {
        $Users = $this->userService->getAllUsers();
        return view('admin.users.index', compact('Users'));
    }

    //*************************************************//
    // * @name   : create
    // * @todo   : Create the form of users
    // * @params :  
    // * @Date   : 19-April-2018
    //************************************************//

    public function create() {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MasterTemplatesRequest $mastertemplaterequest, $id) {
        
    }

    //*************************************************//
    // * @name   : destroy
    // * @todo   : Delete the users
    // * @params :  
    // * @Date   : 20-April-2018
    //************************************************//

    public function destroy($id = NULL) {
        $result = $this->userService->deleteUser($id);
        if ($result === "loginuser") {
            return redirect()->route('users.index')->with("error", "Logged in user can't be deleted");
        } else {
            if ($result == 1) {
                return redirect()->route('users.index')->with('success', 'User deleted successfully');
            } else {
                return redirect()->route('users.index')->with('error', 'Failed to delete user');
            }
        }
    }

    /**
     * get the list from master templates table.
     *
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function getlist(Request $request) {
        
    }

}
