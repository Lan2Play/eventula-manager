<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Error;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Session;
use Storage;
use Image;
use File;
use Helpers;

use App\Game;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;


class GameTemplatesController extends Controller
{
    /**
     * Show Game Templates Index Page
     * @return Redirect
     */
    public function index()
    {
        return view('admin.games.gametemplates.index')
            ->with('gameTemplates', Helpers::getGameTemplates());
    }

    /**
     * Delete Game from Database
     * @return Redirect
     */
    public function deploy(Request $request)
    {
        if(!Helpers::getGameTemplates()->has($request->gameTemplateClass))
        {
            Session::flash('alert-danger', 'Seeder class not found!');
            return Redirect::to('admin/games/gametemplates');
        }

        try {
            DB::beginTransaction();

            Artisan::call('db:seed', ['class'=> $request->gameTemplateClass, '--force' => true]);
            $artisanOutput = Artisan::output();

            if (in_array("Error", str_split($artisanOutput, 5))) {
                throw new Exception($artisanOutput);
            }

            DB::commit();

            Session::flash('alert-success', 'Game template successfully deployed!');
            return Redirect::to('admin/games/gametemplates');

        }
        catch(\Exception | Error $e) {
            DB::rollback();

            Session::flash('alert-danger', 'Template seeding aborted with message: '. $e->getMessage() . '!');
            return Redirect::to('admin/games/gametemplates');
        }
    }
}
