<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminBaseController;
use App\Models\Settings;
use App\Repositories\SettingsRepository;
use Session;
use Illuminate\Support\Facades\Redirect;

class SettingsController extends AdminBaseController {

    protected $settingsRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SettingsRepository $settingsRepository) {
        parent::__construct();

        $this->middleware('auth.admin');

        $this->settingsRepository = $settingsRepository;
    }

    /**
     * to get list of cms pages
     *
     * @param Request $request
     * @return Array
     */
    public function index() {
        $results = $this->settingsRepository->getValue();
        $settings = $results->isNotEmpty() ? $results->all() : [];
        return view('admin.settings', [
            'settings' => $settings,
        ]);
    }

 
    /**
     * To update cms page
     *
     * @param CmsPages $terms
     * @return Array
     */
    public function updatePage(CmsPage $terms) {
        return view('cms_page.updatepage', [
            'today' => $this->today,
            'activeMenu' => 'manage-cms-pages',
            'terms' => $terms,
        ]);
    }

    /**
     * save content of settings pages @addtime
     *
     * @param Request $request
     * @return page
     */
    public function save(Request $request) {
        $request=$request->all();
        $this->settingsRepository->update($request);

        Session::flash('message', 'Settings Updated successfully');
        return Redirect::to('admin/settings');
    }

  


}
