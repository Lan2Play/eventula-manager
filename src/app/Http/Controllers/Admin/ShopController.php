<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Session;
use Settings;
use Colors;
use Storage;
use Image;

use App\ShopItem;
use App\ShopItemImage;
use App\ShopItemCategory;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Show Shop Index Page
     * @return View
     */
    public function index()
    {
        return view('admin.shop.index')
            ->with('isShopEnabled', Settings::isShopEnabled())
            ->with('categories', ShopItemCategory::paginate(10, ['*'], 'sc'))
            ->with('items', ShopItem::paginate(20, ['*'], 'it'));
    }

    /**
     * Show Shop Category Page
     * @return View
     */
    public function showCategory(ShopItemCategory $category)
    {
        return view('admin.shop.category')
            ->with('isShopEnabled', Settings::isShopEnabled())
            ->with('category', $category)
            ->with('items', $category->items()->paginate());
    }

    /**
     * Show Shop Item Page
     * @return View
     */
    public function showItem(ShopItemCategory $category, ShopItem $item)
    {
        return view('admin.shop.item')
            ->with('isShopEnabled', Settings::isShopEnabled())
            ->with('category', $category)
            ->with('item', $item);
    }

 	/**
     * Store Shop Category
     * @param $request
     * @return Redirect
     */
    public function storeCategory(Request $request)
    {
    	$rules = [
    		'name' => 'required|unique:shop_item_categories,name',
    	];
    	$messages = [
    		'name.required' => 'Category Name is Required.',
    		'name.unique' => 'Category Name must be Unique.'
    	];
    	$this->validate($request, $rules, $messages);

	  	if (!ShopItemCategory::create(['name' => $request->name])) {
  		 	Session::flash('alert-danger', 'Cannot create Category!');
            return Redirect::to('/admin/shop/');
	  	}
        Session::flash('alert-success', 'Successfully created Category!');
        return Redirect::to('/admin/shop/');
    }

    /**
     * Delete Shop Category
     * @param ShopItemCategory $category
     * @param $request
     * @return Redirect
     */
    public function deleteCategory(ShopItemCategory $category, Request $request)
    {
        foreach ($category->items as $item) {
            foreach ($item->images as $image) {
                if (!$image->delete()) {
                    Session::flash('alert-danger', 'Cannot delete Image!');
                    return Redirect::back();
                }
            }
            if (!$item->delete()) {
                Session::flash('alert-danger', 'Cannot delete Category!');
                return Redirect::to('/admin/shop/');
            }
        }
        if (!$category->delete()) {
            Session::flash('alert-danger', 'Cannot delete Category!');
            return Redirect::to('/admin/shop/');
        }
        Session::flash('alert-success', 'Successfully deleted Category!');
        return Redirect::to('/admin/shop/');
    }

    /**
     * Update Shop Category
     * @param ShopItemCategory $category
     * @param $request
     * @return Redirect
     */
    public function updateCategory(ShopItemCategory $category, Request $request)
    {
        $rules = [
            'name'      => 'filled',
            'order'     => 'integer',
            'status'    => 'in:draft,published,hidden',
        ];
        $messages = [
            'name.filled'   => 'Category Name cannot be blank.',
            'name.unique'   => 'Category Name must be Unique.',
            'order.integer' => 'Order must be a number',
            'status.in'     => 'Status must be Draft, Publushed or Hidden',
        ];
        $this->validate($request, $rules, $messages);

        $category->name = @$request->name;
        $category->order = @$request->order;
        $category->status = @strtoupper($request->status);

        if (!$category->save()) {
            Session::flash('alert-danger', 'Cannot update Category!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully updated Category!');
        return Redirect::back();
    }

    /**
     * Store Shop Item
     * @param $request
     * @return Redirect
     */
    public function storeItem(Request $request)
    {
    	$rules = [
    		'name'          => 'required',
    		'stock' 		=> 'integer',
    		'category_id'   => 'required|exists:shop_item_categories,id',
    		'price'         => 'numeric',
    		'price_credit' 	=> 'numeric',
    	];
    	$messages = [
    		'name.required' 	    => 'Item Name is Required.',
    		'stock.integer'         => 'Stock must be a number.',
    		'category_id.required' 	=> 'A Category is required.',
    		'category_id.exists'    => 'A Category must exist.',
    		'price.numeric'         => 'Real Price must be a number.',
    		'price_credit.numeric' 	=> 'Credit Price must be a number.',
    	];
    	$this->validate($request, $rules, $messages);

        $price = $request->price;
        if ($request->price == 0) {
            $price = null;
        }
        $price_credit = $request->price_credit;
        if ($request->price_credit == 0) {
            $price_credit = null;
        }

    	$params = [
    		'name'                    => $request->name,
    		'stock'                   => $request->stock,
    		'shop_item_category_id'	  => $request->category_id,
    		'price'                   => $price,
    		'price_credit'            => $price_credit,
    		'added_by'                => Auth::id(),
            'description'             => @$request->description,
    	];
	  	if (!$shopItem = ShopItem::create($params)) {
  		 	Session::flash('alert-danger', 'Cannot save Item!');
            return Redirect::to('admin/shop/');
	  	}
        $params = [
            'default' => true,
            'shop_item_id' => $shopItem->id,
        ];
        if (!ShopItemImage::create($params)) {
            $shopItem->destroy();
            Session::flash('alert-danger', 'Cannot save Item!');
            return Redirect::to('admin/shop/');
        }
        Session::flash('alert-success', 'Successfully saved Item!');
        return Redirect::to('admin/shop/' . $shopItem->category->slug . '/' . $shopItem->slug);
    }

    /**
     * Update Shop Item
     * @param ShopItemCategory $category
     * @param ShopItem $item
     * @param $request
     * @return Redirect
     */
    public function updateItem(ShopItemCategory $category, ShopItem $item, Request $request)
    {
        $rules = [
            'name'          => 'filled',
            'stock'         => 'integer',
            'category_id'   => 'required|exists:shop_item_categories,id',
            'price'         => 'numeric',
            'price_credit'  => 'numeric',
            'featured'      => 'boolean',
            'status'        => 'in:draft,published,hidden'
        ];
        $messages = [
            'name.filled'           => "Name cannot be empty",
            'stock.integer'         => 'Stock must be a number.',
            'category_id.required'  => 'A Category is required.',
            'category_id.exists'    => 'A Category must exist.',
            'price.numeric'         => 'Real Price must be a number.',
            'price_credit.numeric'  => 'Credit Price must be a number.',
            'featured.boolean'      => 'Featured must be a boolean.',
            'status.in'             => 'Status must be Draft, Published or Hidden.',
        ];
        $this->validate($request, $rules, $messages);

        $price = $request->price;
        if ($request->price == 0) {
            $price = null;
        }
        $price_credit = $request->price_credit;
        if ($request->price_credit == 0) {
            $price_credit = null;
        }

        $item->name = @$request->name;
        $item->stock = @$request->stock;
        $item->shop_item_category_id = @$request->category_id;
        $item->price = @$price;
        $item->price_credit = @$price_credit;
        $item->description = @$request->description;
        $item->featured = @$request->featured;
        $item->status = @strtoupper($request->status);
        if (!$item->save()) {
            Session::flash('alert-danger', 'Cannot update Item!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully updated Item!');
        return Redirect::to('admin/shop/' . $category->slug . '/' . $item->slug);
    }

    /**
     * Delete Shop Item
     * @param ShopItemCategory $category
     * @param ShopItem $item
     * @param $request
     * @return Redirect
     */
    public function deleteItem(ShopItemCategory $category, ShopItem $item, Request $request)
    {
        foreach ($item->images as $image) {
            if (!$image->delete()) {
                Session::flash('alert-danger', 'Cannot delete Image!');
                return Redirect::back();
            }
        }
        if (!$item->delete()) {
            Session::flash('alert-danger', 'Cannot delete Item!');
            return Redirect::to('/admin/shop/');
        }
        Session::flash('alert-success', 'Successfully deleted Item!');
        return Redirect::to('admin/shop/' . $category->slug);
    }

    /**
     * Upload Shop Item Image
     * @param ShopItemCategory $category
     * @param ShopItem $item
     * @param $request
     * @return Redirect
     */
    public function uploadItemImage(ShopItemCategory $category, ShopItem $item, Request $request)
    {
        $rules = [
            'image.*'   => 'image',
        ];
        $messages = [
            'image.*.image' => 'Item Image must be of Image type',
        ];
        $this->validate($request, $rules, $messages);
        $destinationPath = '/storage/images/shop/' . $category->slug; // upload path
        Storage::disk('public')->makeDirectory('/images/shop/' . $category->slug . '/', 0777, true, true);
        $files = $request->file('images');
        //Keep a count of uploaded files
        $fileCount = count($files);
        //Counter for uploaded files
        $uploadcount = 0;
        foreach ($files as $file) {
            $imageName  = $item->slug . '-' . $file->getClientOriginalName();
            Image::read($file)->save(public_path() . $destinationPath . $imageName);
            $item->addImage($destinationPath . $imageName);
            $uploadcount++;
        }
        if ($uploadcount != $fileCount) {
            Session::flash('alert-danger', 'Cannot upload Image!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully uploaded Image!');
        return Redirect::to('admin/shop/' . $category->slug . '/' . $item->slug);
    }

    /**
     * Update Shop Item Image
     * @param ShopItemCategory $category
     * @param ShopItem $item
     * @param ShopItemImage $image
     * @param $request
     * @return Redirect
     */
    public function updateItemImage(ShopItemCategory $category, ShopItem $item, ShopItemImage $image, Request $request)
    {
        $rules = [
            'order'   => 'numeric',
        ];
        $messages = [
            'order.numeric' => 'Order must be a number',
        ];
        $this->validate($request, $rules, $messages);
        $image->default = false;
        if (isset($request->default) && $request->default) {
            if ($currentDefault = ShopItemImage::where('shop_item_id', $item->id)->where('default', true)->first()) {
                if ($currentDefault->id != $image->id) {
                    $currentDefault->default = false;
                    if (!$currentDefault->save()) {
                        Session::flash('alert-danger', 'Cannot update Image! Cannot remove old default image.');
                        return Redirect::back();
                    }
                }
            }
            $image->default = true;
        }
        if (isset($request->order)) {
            $image->order = $request->order;
        }
        if (!$image->save()) {
            Session::flash('alert-danger', 'Cannot update Image!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully updated Image!');
        return Redirect::to('admin/shop/' . $category->slug . '/' . $item->slug);
    }

    /**
     * Delete Shop Item Image
     * @param ShopItemCategory $category
     * @param ShopItem $item
     * @param ShopItemImage $image
     * @param $request
     * @return Redirect
     */
    public function deleteItemImage(ShopItemCategory $category, ShopItem $item, ShopItemImage $image, Request $request)
    {
        if (!$image->delete()) {
            Session::flash('alert-danger', 'Cannot delete Image!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully delete Image!');
        return Redirect::to('admin/shop/' . $category->slug . '/' . $item->slug);
    }

}
