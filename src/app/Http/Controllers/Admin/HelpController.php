<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Storage;
use Validator;
use Session;
use Settings;
use Colors;
use File;

use App\User;
use App\Event;
use App\HelpCategory;
use App\HelpCategoryEntry;
use App\HelpCategoryEntryAttachment;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HelpController extends Controller
{
    /**
     * Show Help Index Page
     * @return view
     */
    public function index()
    {
        return view('admin.help.index')
            ->with('helpCategorys', HelpCategory::paginate(20))
            ->with('isHelpEnabled', Settings::isHelpEnabled())
        ;
    }

    /**
     * Show Help Page
     * @return view
     */
    public function show(HelpCategory $helpCategory)
    {
        return view('admin.help.show')
            ->with('helpCategory', $helpCategory)
            ->with('entrys', $helpCategory->entrys()->paginate(10))
            ->with('isHelpEnabled', Settings::isHelpEnabled())
        ;
    }

    /**
     * Store Helpcategory to DB
     * @param  Request $request
     * @return Redirect
     */
    public function store(Request $request)
    {
        $rules = [
            'name'          => 'required',
            'description'   => 'required'
        ];
        $messages = [
            'name.required'         => 'Name is required',
            'description.required'  => 'Description is required'
        ];
        $this->validate($request, $rules, $messages);

        $helpCategory              = new HelpCategory();
        $helpCategory->name        = $request->name;
        $helpCategory->description = $request->description;

        if (!$helpCategory->save()) {
            Session::flash('alert-danger', 'Cannot save HelpCategory!');
            return Redirect::to('admin/help');
        }

        Session::flash('alert-success', 'Successfully saved HelpCategory!');
        return Redirect::to('admin/help/' . $helpCategory->slug);
    }

    /**
     * Update Helpcategory
     * @param  HelpCategory           $helpCategory
     * @param  HelpCategoryEntry|null $entry
     * @param  Request                $request
     * @return Redirect
     */
    public function update(HelpCategory $helpCategory, HelpCategoryEntry $entry = null, Request $request)
    {
        $rules = [
            'name'          => 'filled',
            'description'   => 'filled',
            'status'        => 'in:draft,published',
        ];
        $messages = [
            'name.filled'           => 'Name cannot be empty',
            'description.filled'    => 'Description cannot be empty',
            'status.in'             => 'Status must be draft or published',
        ];
        $this->validate($request, $rules, $messages);

        if (isset($request->name)) {
            $helpCategory->name        = $request->name;
        }

        if (isset($request->description)) {
            $helpCategory->description = $request->description;
        }

        if (isset($request->status)) {
            $helpCategory->status      = $request->status;
        }

        if (isset($request->event_id)) {
            $helpCategory->event_id    = $request->event_id;
        }

        if (!$helpCategory->save()) {
            Session::flash('alert-danger', 'Cannot update HelpCategory!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully updated HelpCategory!');
        return Redirect::back();
    }

    /**
     * Delete HelpCategory
     * @param  HelpCategory $helpCategory
     * @return Redirect
     */
    public function destroy(HelpCategory $helpCategory)
    {
        if (!$helpCategory->delete()) {
            Session::flash('alert-danger', 'Cannot delete Gallery!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully deleted Gallery!');
        return Redirect::back();
    }

    /**
     * Create Helpentry
     * @param  HelpCategory $helpCategory
     * @param  Request      $request
     * @return Redirect
     */
    public function addHelpEntry(HelpCategory $helpCategory, Request $request)
    {
        $rules = [
            'name'          => 'filled',
            'content'   => 'filled'
        ];
        $messages = [
            'name.filled'           => 'Name cannot be empty',
            'content.filled'    => 'Description cannot be empty'
        ];
        $this->validate($request, $rules, $messages);
            $entry                          = new HelpCategoryEntry();
            $entry->display_name            = $request->name;
            $entry->content                 = $request->content;
            $entry->nice_name               = Str::slug(strtolower(str_replace(' ', '-', $request->name)));
            $entry->help_category_id        = $helpCategory->id;

        if (!$entry->save()) {
            Session::flash('alert-danger', 'creation of entry unsuccessful!');
            return Redirect::to('admin/help/' . $helpCategory->slug);
        }

        Session::flash('alert-success', 'creation of entry successful!');
        return Redirect::to('admin/help/' . $helpCategory->slug);
    }

    /**
     * Delete entry from HelpCategory
     * @param  HelpCategory      $helpCategory
     * @param  HelpCategoryEntry $entry
     * @return Redirect
     */
    public function destroyHelpEntry(HelpCategory $helpCategory, HelpCategoryEntry $entry)
    {
        if (!$entry->delete()) {
            Session::flash('alert-danger', 'Cannot delete entry!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully deleted entry!');
        return Redirect::back();
    }

    /**
     * Update entry from HelpCategory
     * @param  HelpCategory      $helpCategory
     * @param  HelpCategoryEntry $entry
     * @param  Request           $request
     * @return Redirect
     */
    public function updateHelpEntry(HelpCategory $helpCategory, HelpCategoryEntry $entry, Request $request)
    {
        //DEBUG - Refactor - replace iamge name as well!
        $entry->display_name  = $request->name;
        $entry->nice_name     = Str::slug(strtolower(str_replace(' ', '-', $request->name)));
        $entry->content          = $request->content;

        if (!$entry->save()) {
            Session::flash('alert-danger', 'Could not update entry!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully updated entry!');
        return Redirect::back();
    }

    /**
     * Upload Attachements to HelpCategoryEntry
     * @param  HelpCategory         $helpCategory
     * @param  HelpCategoryEntry    $entry
     * @param  Request              $request
     * @return Redirect
     */
    public function uploadFiles(HelpCategory $helpCategory, HelpCategoryEntry $entry, Request $request)
    {
        $rules = [
            'attachments.*'   => 'required|max:20000',

        ];
        $messages = [
            'attachments.*.required' => 'Please upload an attachment',
            'attachments.*.max' => 'Sorry! Maximum allowed size for an attachment is 20MB',
        ];
        $this->validate($request, $rules, $messages);

        $files = $request->file('attachments');
        //Keep a count of uploaded files
        $fileCount = count($files);
        //Counter for uploaded files
        $uploadcount = 0;
        $destinationPathFiles = '/attachments/help/' . $entry->id . '/';
        $destinationPath = '/storage'. $destinationPathFiles;
        if ($request->file('attachments') && !File::exists(public_path() . $destinationPath)) {
            File::makeDirectory(public_path() . $destinationPath, 0777, true);
        }
        foreach ($files as $file) {
            $attachmentName  = $file->getClientOriginalName();

            $attachment                         = new HelpCategoryEntryAttachment();
            $attachment->display_name           = $attachmentName;
            $attachment->nice_name              = $attachment->url = strtolower(str_replace(' ', '-', $attachmentName));
            $attachment->help_category_entry_id = $entry->id;
            $ext = preg_replace('/^.*\.([^.]+)$/D', '$1', $attachmentName);

            Storage::disk('public')->putFileAs(
                    $destinationPathFiles,
                    $file,
                    $attachmentName
            );

            $attachment->path = $destinationPath . $attachmentName;

            if ($attachment->save()){
                $uploadcount++;
            }

        }
        if ($uploadcount != $fileCount) {
            Session::flash('alert-danger', 'Upload unsuccessful check attachments!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Upload successful!');
        return Redirect::back();
    }

    /**
     * Delete Attachment from Help Category Entry
     * @param  HelpCategory                 $helpCategory
     * @param  HelpCategoryEntry            $entry
     * @param  HelpCategoryEntryAttachment  $attachment
     * @return Redirect
     */
    public function destroyFile(HelpCategory $helpCategory, HelpCategoryEntry $entry, HelpCategoryEntryAttachment $attachment)
    {
        $destinationPathFile = '/attachments/help/' . $entry->id . '/' . $attachment->display_name;

        if (!$attachment->delete()) {
            Session::flash('alert-danger', 'Cannot delete Attachment!');
            return Redirect::back();
        }

        if (!Storage::disk('public')->delete($destinationPathFile)) {
            Session::flash('alert-danger', 'Cannot delete Attachment File, check file system!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully deleted Attachment!');
        return Redirect::back();
    }

    /**
     * Update Attachment from Gallery
     * @param  HelpCategory                 $helpCategory
     * @param  HelpCategoryEntry            $entry
     * @param  HelpCategoryEntryAttachment  $attachment
     * @param  Request                      $request
     * @return Redirect
     */
    public function updateFile(HelpCategory $helpCategory, HelpCategoryEntry $entry, HelpCategoryEntryAttachment $attachment, Request $request)
    {
        //DEBUG - Refactor - replace attachement name as well!
        $attachment->display_name  = $request->name;
        $attachment->nice_name     = strtolower(str_replace(' ', '-', $request->name));
        $attachment->desc          = $request->desc;

        if (!$attachment->save()) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully updated!');
        return Redirect::back();
    }
}
