<?php

namespace Cat\Console\Commands;

use Cat\Console\Commands\CatEater\FunctionEater;
use Cat\Console\Commands\CatEater\MacroEater;
use Cat\Console\Commands\CatEater\TraitEater;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Printer;
use Winter\LaravelConfigWriter\ArrayFile;

class CatEatCommand extends Command
{
    protected $signature = 'cat:eat {name : 名稱} {app_path : 程式位置} {--F|function} {--T|trait}';

    protected $description = '餵給cat一個命令';

    protected array $tags = [
        'general',
        'network'
    ];
    private string $namespace = '';
    private string $className;

    protected $app_path;

    public function handle()
    {
        $name = lcfirst($this->argument('name'));
        $this->app_path = $this->argument('app_path');

        $app_title = '';
        while (!$app_title) {
            $app_title = $this->ask('Please enter title', '');
        }

        $app_description = '';
        while (!$app_description) {
            $app_description = $this->ask('Please enter description', '');
        }

        $tag = $this->anticipate('Please enter tag', $this->tags, 'general');


        $function = $this->option('function');
        $trait = $this->option('trait');

        if($function){
            $eater = new FunctionEater($name, $this->app_path, $tag, $app_title, $app_description);
        }elseif($trait){
            $eater = new TraitEater($name, $this->app_path, $tag, $app_title, $app_description);
        }else{
            $eater = new MacroEater($name, $this->app_path, $tag, $app_title, $app_description);
        }
        $eater->handle($this);


    }
}
