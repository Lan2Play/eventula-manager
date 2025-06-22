<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HelpCategoryEntry extends Model
{
    use HasFactory;
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'help_category_entrys';

    /**
    * The attributes excluded from the model's JSON form.
    *
    * @var array
    */
    protected $hidden = array(
        'created_at',
        'updated_at'
    );

    /*
    * Relationships
    */
    public function helpCategory()
    {
        return $this->belongsTo('App\HelpCategory');
    }

    public function attachments()
    {
        return $this->hasMany('App\HelpCategoryEntryAttachment');
    }

        /**
     * Delete Attachment from Help Category Entry
     * @return bool
     */
    public function hasAttachment() {

        foreach ($this->attachments as $attachment){
            return true;            
        }
        return false;

    }
}
