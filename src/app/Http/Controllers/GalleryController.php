<?php

namespace App\Http\Controllers;

use DB;
use Auth;

use App\Event;
use App\GalleryAlbum;
use App\GalleryAlbumImage;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Show Gallery Index Page
     * @return View
     */
    public function index()
    {
        $event = Event::where('start', '>=', date("Y-m-d 00:00:00"))->first();
        $albums = GalleryAlbum::all();
        return view('gallery.index')
            ->with('albums', $albums)
            ->with('event', $event);
    }

    /**
     * Show Gallery Page
     * @param  GalleryAlbum $album
     * @return View
     */
    public function show(GalleryAlbum $album)
    {
        return view('gallery.show')
            ->with('album', $album);
    }
}
