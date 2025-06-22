<?php

namespace App;

use Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use GuzzleHttp\Client;
use Lanops\Challonge\Challonge;

use Cviebrock\EloquentSluggable\Sluggable;

class GameServerCommandParameter extends Model
{
    use HasFactory;
    use Sluggable;

    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'game_server_command_parameters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'game',
        'options'
    ];

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
    public function game()
    {
        return $this->belongsTo('App\Game');
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
    
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getParameterSelectArray()
    {
        $explodedVariableParts = explode(";", $this->options);
        return $explodedVariableParts;
    }

    public static function getParameter($name){
        return GameServerCommandParameter::where('name', $name)->first();
    }

    public static function getParameters($command){
        $result = array();
        $matches = array();
        preg_match_all('/{>(?P<parameter>[^{^}]*)}/', $command, $matches);
        foreach ($matches['parameter'] as $key => $match) {
            
            $parameter = GameServerCommandParameter::getParameter($match);
            if($parameter)
            {
                $result[$parameter->id] = $parameter;
            }
        }

        return $result;
    }
}
