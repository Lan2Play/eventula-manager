<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class SearchController extends Controller
{
    /**
     * Autocomplete for users
     * TODO: move this to a secured middleware/endpoint
     * @return \Illuminate\Http\Response
     */
    public function usersAutocomplete(Request $request)
    {

        $data = User::select("id", "username")
                ->where("username","LIKE","%{$request->input('query')}%")
                ->get();

        return json_encode($data);
    }
}
