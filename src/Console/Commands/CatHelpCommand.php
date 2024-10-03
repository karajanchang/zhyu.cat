<?php

namespace Cat\Console\Commands;

use Cat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class CatHelpCommand extends Command
{
    protected $signature = 'cat:help {name : 名稱} {--F|function} {--T|trait}';

    protected $description = '拿到該命令的説明';

    protected $name;

    public function handle(){
        $this->name = $this->argument('name');

        $function = $this->option('function');
        $trait = $this->option('trait');

        if($function){
            $this->handelFunctions();
        }elseif($trait){
            $this->handelTraits();
        }else{
            $this->handelMacros();
        }

    }

    private function handelFunctions()
    {
        Cat::funcHelp($this->name);
    }

    private function handelMacros()
    {
        $name = lcfirst($this->name);
        $macros = Config::get('zhyu.cat.macros', []);

        if(!isset($macros[$name])){
            $this->error('無此命令');
            return 0;
        }
        $macro = app($macros[$name]);
        $this->info($macro->getTitle());
        $this->info($macro->getDescription());
    }
    private function handelTraits()
    {
        Cat::traitHelp($this->name);
    }
}