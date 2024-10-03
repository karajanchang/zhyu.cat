<?php

namespace Cat\Service;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Traits\Macroable;

class CatService
{
    use Macroable;

    public function connection($method, ...$params)
    {
        // 確保第一個參數是方法名
        if (empty($method) || !is_string($method)) {
            throw new \InvalidArgumentException('The first parameter must be a method name string.');
        }

        // 獲取參數
        $argument = $params[0] ?? null; // Get the first parameter, or null if not provided

        // 使用宏來調用 CheckIpRange
        if (static::hasMacro($method)) {
            return $this->macroCall($method, [$argument]);
        }

        throw new \BadMethodCallException("Method {$method} is not a macro.");
    }

    public function func($params)
    {
        try {
            return call_user_func($params);
        }catch (\Exception $e){
            echo $e->getMessage();
        }
    }

    public function funcHelp(...$params)
    {
        try {
            $reflection = new \ReflectionFunction($params[0]);
            echo $reflection->getName() . "\n";
            echo $reflection->getDocComment();

        }catch (\Exception $e){
            echo $e->getMessage();
        }
    }

    public function traitHelp(...$params)
    {
        $traits = Config::get('zhyu.cat.traits', []);
        $tag = null;
        $trait = $params[0];
        foreach($traits as $key => $traitArray){
            if(in_array($trait, $traitArray)){
                $tag = $key;
                break;
            }
        }

        if(is_null($tag)){
            throw new \Exception($params[0].', Trait not found');
        }

        try {
            $reflection = new \ReflectionClass('Cat\Traits\\' . $tag . '\\' . $params[0]);
            echo $reflection->getName() . "\n";
            echo $reflection->getDocComment();

            $methods = $reflection->getMethods();
            foreach ($methods as $method) {
                echo $method;
            }
            return 0;
        }catch (\Exception $e){
            echo $e->getMessage();
            return 0;
        }
    }


    public function __call($method, $parameters)
    {
        if(method_exists($this, $method)){
           return $this->{$method}($parameters);
        }
        // 獲取連接結果
        return $this->connection($method, $parameters);
    }
}