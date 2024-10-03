<?php

namespace tests\Feature;

use Cat\CatServiceProvider;
use Cat\Facades\CatFacade;
use Orchestra\Testbench\Concerns\WithWorkbench;

class CatFacadeTest extends \Orchestra\Testbench\TestCase
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
        $this->assertEquals('func', \Cat::func());
    }

    public function test_cat_facade_eat_method()
    {
        // 使用 Facade 調用 meow 方法
        $this->assertEquals('eat', \Cat::eat());
    }
}