<?php

namespace App\Http\Controllers\Admin\Events;

use DB;
use Auth;
use Session;
use Storage;

use App\Event;
use App\EventVenue;
use App\EventVenueImage;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use Sirprize\PostalCodeValidator\Validator as PostcodeValidator;

class VenuesController extends Controller
{
    /**
     * Show Venues Index Page
     * @return View
     */
    public function index()
    {
        return view('admin.events.venues.index')
            ->with('venues', EventVenue::paginate(10))
        ;
    }

    /**
     * Show Venue Page
     * @param  EventVenue $venue
     * @return View
     */
    public function show(EventVenue $venue)
    {
        return view('admin.events.venues.show')
            ->with('venue', $venue);
    }

    /**
     * Store Venue to Database
     * @param  Request $request
     * @return Redirect
     */
    public function store(Request $request)
    {
        $rules = [
            'name'              => 'required',
            'address_1'         => 'required',
            'address_street'    => 'required',
            'address_city'      => 'required',
            'address_postcode'  => 'required',
            'address_country'   => 'required',
            'image.*'           => 'image',
        ];
        $messages = [
            'name.required'             => 'Venue Name is Required',
            'address_1.required'        => 'Address is Required',
            'address_street.required'   => 'Street name is Required',
            'address_city.required'     => 'City name is Required',
            'address_postcode.required' => 'Postcode is Required',
            'address_country.required'  => 'Country is Required',
            'image.*.image'             => 'Venue Image must be of Image type',
        ];
        $this->validate($request, $rules, $messages);

        // Validation
        $validCountry = false;
        $countryCode = null;
        $country = null;
        foreach (json_decode(file_get_contents("http://country.io/names.json"), true) as $thisCountryCode => $thisCountry) {
            if (strtolower($thisCountry) == strtolower($request->address_country) || strtolower($thisCountryCode) == strtolower($request->address_country)) {
                $validCountry = true;
                $countryCode = $thisCountryCode;
                $country = $thisCountry;
            }
        }
        if (!$validCountry) {
            Session::flash('alert-danger', 'That country appears to be invalid. Please use a valid one.');
            return Redirect::back()->with('input', $request->input())->with('error', 'That country appears to be invalid. Please use a valid one.');
        }

        if (isset($request->address_postcode) && $request->address_postcode != null && trim($request->address_postcode) != '') {
            $validator = new PostcodeValidator();
            if (!$validator->isValid($countryCode, $request->address_postcode, true)) {
                Session::flash('alert-danger', 'That postcode appears to be invalid. Please use a valid one.');
                return Redirect::back();
            }
        }

        $venue                      = new EventVenue();
        $venue->display_name        = $request->name;
        $venue->address_1           = $request->address_1;
        $venue->address_2           = $request->address_2;
        $venue->address_street      = $request->address_street;
        $venue->address_city        = $request->address_city;
        $venue->address_postcode    = $request->address_postcode;
        $venue->address_country     = $request->address_country;

        if (!$venue->save()) {
            Session::flash('alert-danger', 'Cannot save Venue before image upload!');
            return Redirect::back();
        }

        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $venue->images()->create([
                    'path' => str_replace(
                        'public/',
                        '/storage/',
                        Storage::put(
                            'public/images/venues/' . $venue->slug,
                            $image
                        )
                    ),
                ]);
            }
        }

        if (!$venue->save()) {
            Session::flash('alert-danger', 'Cannot save Venue!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully saved venue!');
        return Redirect::to('admin/venues/' . $venue->slug);
    }

    /**
     * Update Venue
     * @param  EventVenue $venue
     * @param  Request    $request
     * @return Redirect
     */
    public function update(EventVenue $venue, Request $request)
    {
        $rules = [
            'name'              => 'filled',
            'address_1'         => 'filled',
            'address_street'    => 'filled',
            'address_city'      => 'filled',
            'address_postcode'  => 'filled',
            'address_country'   => 'filled',
            'image.*'           => 'image',
        ];
        $messages = [
            'name.filled'               => 'Venue Name cannot be empty',
            'address_1.filled'          => 'Address cannot be empty',
            'address_street.filled'     => 'Street name cannot be empty',
            'address_city.filled'       => 'City name cannot be empty',
            'address_postcode.filled'   => 'Postcode cannot be empty',
            'address_country.filled'    => 'Country cannot be empty',
            'image.*.image'             => 'Venue Image must be of Image type',
        ];
        $this->validate($request, $rules, $messages);

        if (isset($request->name)) {
            $venue->display_name        = $request->name;
        }

        if (isset($request->address_1)) {
            $venue->address_1           = $request->address_1;
        }

        if (isset($request->address_2)) {
            $venue->address_2           = $request->address_2;
        }

        if (isset($request->address_street)) {
            $venue->address_street      = $request->address_street;
        }

        if (isset($request->address_city)) {
            $venue->address_city        = $request->address_city;
        }

        if (isset($request->address_postcode) || isset($request->address_country)) {
            $validCountry = false;
            $countryCode = null;
            $country = null;
            foreach (json_decode(file_get_contents("http://country.io/names.json"), true) as $thisCountryCode => $thisCountry) {
                if (strtolower($thisCountry) == strtolower($request->address_country) || strtolower($thisCountryCode) == strtolower($request->address_country)) {
                    $validCountry = true;
                    $countryCode = $thisCountryCode;
                    $country = $thisCountry;
                }
            }
            if (!$validCountry) {
                Session::flash('alert-danger', 'That country appears to be invalid. Please use a valid one.');
                return Redirect::back()->with('input', $request->input())->with('error', 'That country appears to be invalid. Please use a valid one.');
            }

            if (isset($request->address_postcode) && $request->address_postcode != null && trim($request->address_postcode) != '') {
                $validator = new PostcodeValidator();
                if (!$validator->isValid($countryCode, $request->address_postcode, true)) {
                    Session::flash('alert-danger', 'That postcode appears to be invalid. Please use a valid one.');
                    return Redirect::back();
                }
            }
            $venue->address_postcode    = $request->address_postcode;
            $venue->address_country     = $request->address_country;

        }

        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $venue->images()->create([
                    'path' => str_replace(
                        'public/',
                        '/storage/',
                        Storage::put('public/images/venues/' . $venue->slug, $image)
                    ),
                ]);
            }
        }

        if (!$venue->save()) {
            Session::flash('alert-danger', 'Cannot update Venue!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully updated Venue!');
        return Redirect::back();
    }

    /**
     * Delete Venue from Database
     * @param  EventVenue $venue
     * @return Redirect
     */
    public function destroy(EventVenue $venue)
    {
        if (!$venue->events->isEmpty()) {
            Session::flash('alert-danger', 'Cannot delete Venue! - In use by events!');
            return Redirect::back();
        }

        if (!$venue->delete()) {
            Session::flash('alert-danger', 'Cannot delete Venue!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully deleted venue!');
        return Redirect::to('/admin/venues');
    }

    /**
     * Update Venue Image
     * @param  EventVenue      $venue
     * @param  EventVenueImage $image
     * @param  Request         $request
     * @return Redirect
     */
    public function updateImage(EventVenue $venue, EventVenueImage $image, Request $request)
    {
        if (isset($request->description)) {
            $image->description = $request->description;
        }

        if (!$image->save()) {
            Session::flash('alert-danger', 'Could update Image!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully updated Image!');
        return Redirect::back();
    }

    /**
     * Delete Image Venue
     * @param  EventVenue      $venue
     * @param  EventVenueImage $image
     * @return Redirect
     */
    public function destroyImage(EventVenue $venue, EventVenueImage $image)
    {
        if (!$image->delete()) {
            Session::flash('alert-danger', 'Cannot delete Image!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully deleted Image!');
        return Redirect::back();
    }
}
