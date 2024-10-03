<?php

namespace Cat\Console\Commands\CatRemover;

use Cat\Console\Commands\CatRemoveCommand;
use Illuminate\Support\Facades\Config;
use Winter\LaravelConfigWriter\ArrayFile;

class TraitRemover extends AbstractRemover
{
    protected string $src_directory = 'Traits';
    public function handle(CatRemoveCommand $command)
    {
        $configFile  = base_path('vendor/zhyu/cat/config/traits.php');
        $cat_config_traits = Config::get('zhyu.cat.traits', []);
        dump($cat_config_traits);

        $tag = null;
        foreach($cat_config_traits as $_tag => $traits) {
            $key = array_search($this->app_name, $traits);
            dump($key, $this->app_name, $traits);
            if ($key !== false) {
                unset($cat_config_traits[$_tag][$key]);
                $tag = $_tag;
                break;
            }
        }

        if(is_null($tag)){
            $command->error('Trait not found');
            return 0;
        }

        $config = ArrayFile::open($configFile);
        $config->set($cat_config_traits);
        $configContent = $config->render();

        file_put_contents($configFile, $configContent);

        if($this->remove_file===true){
            $this->removeFile($tag);
        }
    }
}