<?php

namespace Cat\Console\Commands;

use Cat\Console\Commands\CatRemover\FunctionRemover;
use Cat\Console\Commands\CatRemover\MacroRemover;
use Cat\Console\Commands\CatRemover\TraitRemover;
use Illuminate\Console\Command;


class CatRemoveCommand extends Command
{
    protected $signature = 'cat:remove {name : 名稱} {--F|function} {--T|trait} {--R|remove_file : 一併移除檔案}';

    protected $description = '移除cat一個命令';

    public function handle()
    {
        $name = lcfirst($this->argument('name'));
        $remove_file = $this->option('remove_file');

        $function = $this->option('function');
        $trait = $this->option('trait');

        if($this->confirm('Are you sure you want to remove?')){
            if($function){
               $remover = new FunctionRemover($name, $remove_file);
            }elseif($trait){
                $remover = new TraitRemover(ucfirst($name), $remove_file);
            }else{
                $remover = new MacroRemover($name, $remove_file);
            }
            $remover->handle($this);
        }
    }



}
