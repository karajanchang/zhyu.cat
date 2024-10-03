<?php

namespace Cat\Console\Commands\CatRemover;

abstract class AbstractRemover implements ContractRemover
{
    protected string $src_directory;
    public function __construct(
        protected string $app_name,
        protected bool $remove_file,
    )
    {
        if(is_null($this->src_directory)){
           throw new \Exception('$src_directory is not set');
        }

    }

    protected function removeFile($tag)
    {
        $file = base_path('vendor/zhyu/cat/src/'.$this->src_directory.'/'.ucfirst($tag).'/'.ucfirst($this->app_name).'.php');
        if(file_exists($file)){
            unlink($file);
        }
    }
}