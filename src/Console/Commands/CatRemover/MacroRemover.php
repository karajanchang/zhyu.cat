<?php

namespace Cat\Console\Commands\CatRemover;

use Cat\Console\Commands\CatRemoveCommand;
use Cat\Console\Commands\ExtractTagFromMacroTrait;
use Illuminate\Support\Facades\Config;
use Winter\LaravelConfigWriter\ArrayFile;

class MacroRemover extends AbstractRemover
{
    use ExtractTagFromMacroTrait;
    protected string $src_directory = 'helpers';
    public function handle(CatRemoveCommand $command)
    {
        $configFile  = base_path('vendor/zhyu/cat/config/macros.php');
        $cat_config_macros = Config::get('zhyu.cat.macros', []);
        if(isset($cat_config_macros[$this->app_name])){
            $macro = $cat_config_macros[$this->app_name];
            $tag = $this->extractTag($macro);
            unset($cat_config_macros[$this->app_name]);
        }else{
            $command->error('Macro not found');
            return 0;
        }

        $classStrings = [];
        foreach($cat_config_macros as $tname => $macro){
            $classStrings[] = $macro;
        }
        $config = ArrayFile::open($configFile);
        $config->set($cat_config_macros);
        $configContent = $config->render();

        foreach($classStrings as $string){
            $escapedClassString = addslashes($string);
            $configContent = str_replace("'$escapedClassString'", "$string::class", $configContent);
        }
        file_put_contents($configFile, $configContent);

        if($this->remove_file===true){
            $this->removeFile($tag);
        }
    }
}