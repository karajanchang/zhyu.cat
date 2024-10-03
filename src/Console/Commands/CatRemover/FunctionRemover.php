<?php

namespace Cat\Console\Commands\CatRemover;

use Cat\Console\Commands\CatRemoveCommand;
use Illuminate\Support\Facades\Config;
use Winter\LaravelConfigWriter\ArrayFile;

class FunctionRemover extends AbstractRemover
{
    protected string $src_directory = 'functions';
    public function handle(CatRemoveCommand $command)
    {
        $configFile  = base_path('vendor/zhyu/cat/config/functions.php');
        $cat_config_functions = Config::get('zhyu.cat.functions', []);

        $tag = null;
        foreach($cat_config_functions as $_tag => $functions) {
            $key = array_search($this->app_name, $functions);
            if ($key !== false) {
                unset($cat_config_functions[$_tag][$key]);
                $tag = $_tag;
                break;
            }
        }

        if(is_null($tag)){
            $command->error('Function not found');
            return 0;
        }

        $config = ArrayFile::open($configFile);
        $config->set($cat_config_functions);
        $configContent = $config->render();

        file_put_contents($configFile, $configContent);

        if($this->remove_file===true){
            $this->removeFile($tag);
        }
    }
}