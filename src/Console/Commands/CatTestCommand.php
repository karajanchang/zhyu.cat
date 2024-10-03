<?php

namespace Cat\Console\Commands;

use Cat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class CatTestCommand extends Command
{
    protected $signature = 'cat:test {name : 名稱} {arguments?* : 參數} {--F|function} {--T|trait}';

    protected $description = '測試cat一個命令';
    public function handle(){
        $name = lcfirst($this->argument('name'));
        $arguments = $this->argument('arguments');

        $arguments = count($arguments) === 1 ? $arguments[0] : $arguments;

        $function = $this->option('function');
        $trait = $this->option('trait');

        try {
            if($function) {
                $result = call_user_func($name);
            }elseif($trait){
                $cat_config = Config::get('zhyu.cat.traits', []);
                $tag = null;
                $name = ucfirst($name);
                foreach($cat_config as $_tag => $traits) {
                    $key = array_search($name, $traits);
                    if(!$key){
                       $tag = $_tag;
                       break;
                    }
                }
                $app = 'Cat\\Traits\\' . ucfirst($tag) . '\\' . $name;
                $res = trait_exists($app);
                $result = $app. ' Trait does not exists.';
                if($res){
                   $result = $app. ' Trait exists, testing is ok.';
                }
            }else {
                $cat = Cat::$name();
                $result = $cat($arguments);
            }
            $this->info('Result: ' . var_export($result, true));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
