<?php

namespace tests\Unit;

use Cat\CatServiceProvider;
use Cat\Facades\CatFacade;
use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\Concerns\WithWorkbench;

class CatHelperTest extends \Orchestra\Testbench\TestCase
{
    use WithWorkbench;

    protected function getPackageProviders($app)
    {
        return [
            CatServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Cat' => CatFacade::class,
        ];
    }

    public function test_cat_facade_func_method()
    {
        // 使用 Facade 調用 meow 方法
        //$this->assertEquals('func', \Cat::func());
        $a = Config::set('cat.macros', ['abc', '123']);
        dump($a);
    }
}