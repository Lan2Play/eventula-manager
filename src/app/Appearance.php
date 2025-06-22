<?php

namespace App;

use Cache;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use ScssPhp\ScssPhp\Compiler;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class Appearance extends Model
{
    use HasFactory;
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'appearance';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array(
        'created_at',
        'updated_at'
    );

    /**
     * Recompile CSS
     * @return Boolean
     */
    public static function cssRecompile()
    {
        @Cache::forget("css_version");
        $scss = new Compiler();
        $scss->setImportPaths('/web/html/resources/assets/sass/');
        $scss->addImportPath('/web/html/');
        $scss->addImportPath('/web/html/node_modules/');

        $cssTemplates = ['app', 'admin'];
        foreach ($cssTemplates as $cssTemplate) {
            // Define the source map options as required
            $sourceMapWriteTo = config('filesystems.disks.compiled-css.root') .
                '/' .
                str_replace("/", "_", $cssTemplate) .
                ".css.map";

            $scss->setSourceMapOptions([
                'sourceMapWriteTo'  => $sourceMapWriteTo,
                'sourceMapFilename' => $cssTemplate . '.css',
                'sourceMapBasepath' => config('filesystems.disks.compiled-css.root'),
                'sourceRoot'        => '/',
            ]);

            // Delete old compiled files
            @Storage::disk('compiled-css')->delete($cssTemplate . '.css');
            @Storage::disk('compiled-css')->delete($cssTemplate . '.css.map');

            // Compile and save the new CSS file
            $scssContent = '@import "' . $cssTemplate . '.scss";';
            $compiledCss = $scss->compileString($scssContent, $sourceMapWriteTo)->getCss();

            if (!Storage::disk('compiled-css')->put($cssTemplate . '.css', $compiledCss)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get CSS Overrides
     * @return Boolean|Storage
     */
    public static function getCssOverride()
    {
        if (!Storage::disk('assets')->exists('sass/stylesheets/app/components/_user-override.scss')) {
            return false;
        }
        return Storage::disk('assets')->get('sass/stylesheets/app/components/_user-override.scss');
    }

    /**
     * Save CSS Overrides
     * @param String $css
     * @return Boolean
     */
    public static function saveCssOverride($css)
    {
        @Cache::forget("css_version");
        if (!Storage::disk('assets')->put('sass/stylesheets/app/components/_user-override.scss', $css)) {
            return false;
        }
        return true;
    }

    /**
     * Get CSS Variable
     * @return Var
     */
    public static function getCssVariables()
    {
        return self::where('type', 'CSS_VAR')->get();
    }

    /**
     * Get CSS Variable
     * @return Var
     */
    public static function getCssVariable($name)
    {
        $clauses = ['type' => 'CSS_VAR', 'key' => $name];
        return self::where($clauses)->first();
    }

    /**
     * Get CSS Variable
     * @return Var
     */
    public static function getCssVersion()
    {
        return Cache::get("css_version", function () {
            $int = random_int(1, 999);
            Cache::forever("css_version", $int);
            return $int;
        });
    }

    /**
     * Save CSS Variable
     * @return Var
     */
    public static function saveCssVariables($variables)
    {
        @Cache::forget("css_version");
        Storage::disk('assets')->put('sass/stylesheets/app/modules/_user-variables.scss', '// User Variable Overrides');
        foreach ($variables as $key => $value) {
            if (!self::saveCssVariableToDatabase($key, $value)) {
                return false;
            }
            if (!self::saveCssVariableToFile($key, $value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Save CSS Variable to Database
     * @return Var
     */
    public static function saveCssVariableToDatabase($key, $value)
    {
        @Cache::forget("css_version");
        $variable = self::where('key', $key)->first();
        if (!isset($variable))
        {
            return false;
        }
        $variable->value = $value;
        if (!$variable->save()) {
            return false;
        }
        return true;
    }

    /**
     * Save CSS Variable to File
     * @return Var
     */
    public static function saveCssVariableToFile($key, $value)
    {
        @Cache::forget("css_version");
        if (!Storage::disk('assets')
            ->append('sass/stylesheets/app/modules/_user-variables.scss', '$' . $key . ': ' . $value . ';')
        ) {
            return false;
        }
        return true;
    }

    /**
     * update the css variables in the database from the file
     * @return Var
     */
    public static function updateDatabaseCssVariablesFromFile()
    {
        $user_variables_file = Storage::disk('assets')->get('sass/stylesheets/app/modules/_user-variables.scss');
        $user_variables_file_lines = explode("\n", $user_variables_file);

        foreach ($user_variables_file_lines as $line){
            if (Str::of($line)->isMatch('/^\$.*:.*;$/'))
            {
                $key = Str::of($line)->match('/(?<=\$).*?(?=:)/')->rtrim();
                $value = Str::of($line)->match('/(?<=:).*?(?=;)/')->ltrim();
                if (!self::saveCssVariableToDatabase($key, $value)) {
                    return false;
                }
            }

            
        }
        return true;
        
    }


}
