<?php

namespace Cat\Console\Commands;

use Cat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class CatListCommand extends Command
{
    use ExtractTagFromMacroTrait;
    protected $signature = 'cat:list {tags?* : 名稱} {--F|function} {--T|trait}';

    protected $description = '列出cat命令';

    protected array $tags = [];
    public function handle(){
        $this->tags = $this->argument('tags');

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
        $cat_config_functions = Config::get('zhyu.cat.functions', []);
        $tagRows = [];
        if(isset($cat_config_functions)){
            foreach($cat_config_functions as $tag => $functions){
                foreach($functions as $function) {
                    if ($this->tags) {
                        if (!in_array($tag, $this->tags)) {
                            continue;
                        }
                    }
                    $reflection = new \ReflectionFunction($function);
                    $tagRows[$tag][$function] = [
                        'file' => $reflection->getFileName(),
                        'title' => '',
                        'description' => ''
                    ];
                }
            }
        }
        $this->dumpTagRows($tagRows);
    }

    private function handelMacros()
    {
        $cat_config_macros = Config::get('zhyu.cat.macros', []);

        try {
            $tagRows = [];
            if(isset($cat_config_macros)){
                foreach($cat_config_macros as $name => $macro){
                    $tag = $this->extractTag($macro);
                    if($this->tags){
                        if(!in_array($tag, $this->tags)){
                            continue;
                        }
                    }
                    $macroApp = app($macro);
                    $tagRows[$tag][$name] = [
                        'file' => get_class($macroApp),
                        'title' => $macroApp->getTitle(),
                        'description' => $macroApp->getDescription()
                    ];
                }
            }
            $this->dumpTagRows($tagRows);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }
    private function handelTraits()
    {
        $cat_config_traits = Config::get('zhyu.cat.traits', []);
        $tagRows = [];
        if(isset($cat_config_traits)){
            foreach($cat_config_traits as $tag => $traits){
                foreach($traits as $trait) {
                    if ($this->tags) {
                        if (!in_array($tag, $this->tags)) {
                            continue;
                        }
                    }
                    $reflection = new \ReflectionClass('Cat\Traits\\' . $tag . '\\' . $trait);
                    $tagRows[$tag][$trait] = [
                        'file' => $reflection->getFileName(),
                        'title' => '',
                        'description' => ''
                    ];
                }
            }
        }
        $this->dumpTagRows($tagRows);
    }

    /**
     * print tag and list records in console
     */
    private function dumpTagRows($tagRows)
    {
        ksort($tagRows);
        foreach($tagRows as $tag => $macros){
            $this->line('Tag: ' . $tag);
            foreach($macros as $name => $macro){
                $this->line('  -- Name: '.$name.' | '.$macro['file'].' | '.$macro['title'].' | '.$macro['description']);
            }
        }
    }
}
