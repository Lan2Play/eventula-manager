<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Session;
use Storage;
use Image;
use File;

use App\User;
use App\Setting;
use App\Appearance;
use App\SliderImage;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AppearanceController extends Controller
{
    /**
     * Show Appearance Index Page
     * @return Redirect
     */
    public function index()
    {
        $cssVariables = Appearance::getCssVariables();
        $sortedCssVariables['primary'] = $cssVariables->filter(function ($item) {
            return false !== stristr($item->key, 'color_primary');
        });
        $sortedCssVariables['secondary'] = $cssVariables->filter(function ($item) {
            return false !== stristr($item->key, 'color_secondary');
        });
        $sortedCssVariables['body'] = $cssVariables->filter(function ($item) {
            return false !== stristr($item->key, 'color_body');
        });
        $sortedCssVariables['header'] = $cssVariables->filter(function ($item) {
            return false !== stristr($item->key, 'color_header');
        });
        return view('admin.settings.appearance')
            ->with('sliderImages', SliderImage::getImages('frontpage'))
            ->with('userOverrideCss', Appearance::getCssOverride())
            ->with('cssVariables', $sortedCssVariables);
    }

    /**
     * Recompile CSS from SCSS
     * @return Redirect
     */
    public function cssRecompile()
    {
        if (!Appearance::cssRecompile()) {
            Session::flash('alert-danger', 'Could recompile CSS. Please try again.');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully recompiled the CSS!');
        return Redirect::back();
    }

    /**
     * Update SCSS user database variables from file
     * @return Redirect
     */
    public function cssUpdateDatabaseFromFile()
    {
        if (!Appearance::updateDatabaseCssVariablesFromFile()) {
            Session::flash('alert-danger', 'Could update database values. Do you maybe have a variable in your file that does not belong there? Please try again.');
            return Redirect::back();
        }
        if (!Appearance::cssRecompile()) {
            Session::flash('alert-danger', 'Could recompile CSS. Please try again.');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully updated the database values!');
        return Redirect::back();
    }

    /**
     * Add Additional CSS Override
     * @param Request $request
     * @return Redirect
     */
    public function cssOverride(Request $request)
    {
        if (config('appearance.disable_admin_appearance_css_settings') == "true")
        {
            Session::flash('alert-danger', 'Could not save CSS because it is disabled by the APPEAR_DISABLE_ADMIN_APPEARANCE_CSS_SETTINGS environment variable. Contact your hoster!');
            return Redirect::back();
        }

        $rules = [
            'css'   => 'required',
        ];
        $messages = [
            'css.required'      => 'CSS is required.',
        ];
        $this->validate($request, $rules, $messages);
        if (!Appearance::saveCssOverride($request->css)) {
            Session::flash('alert-danger', 'Could not save CSS. Please try again.');
            return Redirect::back();
        }
        if (!Appearance::cssRecompile()) {
            Session::flash('alert-danger', 'Could recompile CSS. Please try again.');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully recompiled the CSS!');
        return Redirect::back();
    }

    /**
     * Update CSS Variables
     * @param Request $request
     * @return Redirect
     */
    public function cssVariables(Request $request)
    {
        if (config('appearance.disable_admin_appearance_css_settings') == "true")
        {
            Session::flash('alert-danger', 'Could not save CSS because it is disabled by the APPEAR_DISABLE_ADMIN_APPEARANCE_CSS_SETTINGS environment variable. Contact your hoster!');
            return Redirect::back();
        }
        if (!Appearance::saveCssVariables($request->css_variables)) {
            Session::flash('alert-danger', 'Could not save CSS Variables. Please try again.');
            return Redirect::back();
        }
        if (!Appearance::cssRecompile()) {
            Session::flash('alert-danger', 'Could recompile CSS. Please try again.');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully saved CSS Variables!');
        return Redirect::back();
    }

     /**
     * Update Slider Image
     * @param  Request $request
     * @param  SliderImage $image
     * @return Redirect
     */
    public function sliderUpdate(Request $request, SliderImage $image)
    {
        $rules = [
            'order'   => 'numeric',
        ];
        $messages = [
            'order.numeric' => 'Order must be a number',
        ];
        $this->validate($request, $rules, $messages);
        $image->order = $request->order;
        if (!$image->save()) {
            Session::flash('alert-danger', 'Cannot update Image!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully updated Image!');
        return Redirect::back();
    }

    /**
     * Delete Slider Image
     * @param  Request $request
     * @param  SliderImage $image
     * @return Redirect
     */
    public function sliderDelete(Request $request, SliderImage $image)
    {
        if (
            !Storage::disk('public')->delete(str_replace('/storage', '', $image->path)) ||
            !$image->delete()
        ) {
            Session::flash('alert-danger', 'Cannot delete Image!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully delete Image!');
        return Redirect::back();
    }

    /**
     * Upload Slider Image
     * @param  Request $request
     * @param  SliderImage $image
     * @return Redirect
     */
    public function sliderUpload(Request $request)
    {
        $rules = [
            'image.*'   => 'image',
        ];
        $messages = [
            'image.*.image' => 'Item Image must be of Image type',
        ];
        $this->validate($request, $rules, $messages);
        $destinationPath = '/storage/images/main/slider/' . $request->slider . '/'; // upload path
        $files = $request->file('images');
        //Keep a count of uploaded files
        $fileCount = count($files);
        //Counter for uploaded files
        $uploadcount = 0;
        foreach ($files as $file) {
            $imageName  = $file->getClientOriginalName();
            Image::read($file)->save(public_path() . $destinationPath . $imageName);
            $image = New SliderImage();
            $image->path = $destinationPath . $imageName;
            $image->slider_name = $request->slider;
            $image->order = '5';
            if (!$image->save()) {
                Session::flash('alert-danger', 'Cannot upload Image!');
                return Redirect::back();
            }
            $uploadcount++;
        }
        if ($uploadcount != $fileCount) {
            Session::flash('alert-danger', 'Cannot upload Image!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully uploaded Image!');
        return Redirect::back();
    }
}
