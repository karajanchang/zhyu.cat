<?php

namespace Cat;

use Cat\Console\Commands\CatEatCommand;
use Cat\Console\Commands\CatHelpCommand;
use Cat\Console\Commands\CatInstallCommand;
use Cat\Console\Commands\CatListCommand;
use Cat\Console\Commands\CatRemoveCommand;
use Cat\Console\Commands\CatTestCommand;
use Cat\Service\CatService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class CatServiceProvider extends ServiceProvider
{
    private $commands = [
        CatEatCommand::class,
        CatHelpCommand::class,
        CatInstallCommand::class,
        CatListCommand::class,
        CatRemoveCommand::class,
        CatTestCommand::class,
    ];
    public function register()
    {
        $this->app->singleton('cat', function ($app) {
            return new CatService();
        });

    }

    public function boot()
    {
        // 發布配置文件
//        $this->publishes([
//            __DIR__ . '/../config/cat.php' => config_path('zhyu/cat/cat.php'),
//        ]);

        // 合併配置
        $this->mergeConfigFrom(__DIR__ . '/../config/cat.php', 'zhyu.cat');
        $this->mergeConfigFrom(__DIR__ . '/../config/macros.php', 'zhyu.cat.macros');
        $this->mergeConfigFrom(__DIR__ . '/../config/functions.php', 'zhyu.cat.functions');
        $this->mergeConfigFrom(__DIR__ . '/../config/traits.php', 'zhyu.cat.traits');

        // 註冊宏
        $this->registerMacros();

        // 註冊函數
        $this->registerFunctions();

        // 自動載入命令
        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }
    }

    protected function registerFunctions()
    {
        $functions = Config::get('zhyu.cat.functions', []);
        foreach(array_keys($functions) as $tag) {
            foreach (glob(__DIR__ . '/functions/' . $tag . '/*.php') as $filename) {
                require_once $filename;
            }
        }
    }

    protected function registerMacros()
    {
        // 獲取配置中的宏
        $macros = Config::get('zhyu.cat.macros', []);
        if (is_array($macros)) {
            foreach ($macros as $name => $macro) {
                $this->registerMacro($name, $macro);
            }
        }
    }

    protected function registerMacro($name, $macro)
    {
        // 確認類是否存在
        if (!class_exists($macro)) {
            throw new \InvalidArgumentException("Class {$macro} does not exist.");
        }

        // 註冊宏
        \Cat::macro(lcfirst($name), function (...$params) use ($macro) {
            $instance = app($macro); // 獲取實例
            
            return $instance;
        });
    }
}
