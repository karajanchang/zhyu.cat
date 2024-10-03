<?php

namespace Cat\Console\Commands\CatEater;

use Cat\Console\Commands\CatEatCommand;

abstract class AbstractEater implements ContractEater
{
    protected $src_directory;
    public function __construct(
        protected string $app_name,
        protected string $app_path,
        protected string $tag,
        protected string $app_title,
        protected string $app_description
    )
    {
        $this->createTagDirectory();
    }

    protected function createTagDirectory()
    {
        $dir = base_path('vendor/zhyu/cat/src/'.$this->src_directory.'/' . ucfirst($this->tag));
        if(!file_exists($dir)){
            mkdir($dir);
        }
    }

    protected function getClassName()
    {
        $fileContent = file_get_contents($this->app_path);
        if (preg_match('/class\s+([a-zA-Z0-9_]+)/', $fileContent, $matches)) {
            $this->className = $matches[1];
        } else {
            $this->className = pathinfo($this->app_path, PATHINFO_FILENAME);
        }
        if (preg_match('/namespace\s+([a-zA-Z0-9_\\\\]+)/', $fileContent, $matches)) {
            $this->namespace = $matches[1];
        }
    }

}