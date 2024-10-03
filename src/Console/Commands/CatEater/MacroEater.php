<?php

namespace Cat\Console\Commands\CatEater;

use Cat\Console\Commands\CatEatCommand;
use Illuminate\Support\Facades\Config;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Printer;
use Winter\LaravelConfigWriter\ArrayFile;

class MacroEater extends AbstractEater
{
    protected $src_directory = 'helpers';
    public function handle(CatEatCommand $command)
    {
        $this->getClassName();
        $app = $this->namespace . '\\' . $this->className;
        $command->line("Class Name: " . $app);

        if (class_exists($app)) {
            // 使用 Reflection 获取类信息
            $reflection = new \ReflectionClass($app);
            $command->line("Class Name: " . $reflection->getName());

            $methods = $reflection->getMethods();
            $method_names = [];
            foreach ($methods as $method) {
                $command->line("- " . $method->getName());
                $method_names[] = $method->getName();
            }

            $invoke_method = '';
            while (!$invoke_method) {
                $invoke_method = $command->anticipate('請選擇__invoke方法要用哪個method?', $method_names, '');

                if (!in_array($invoke_method, $method_names)) {
                    $command->error('必須選擇一個有效的方法。');
                    $invoke_method = '';
                }
            }

            $command->info("選擇的method是：{$invoke_method}");

            $this->process($command, $invoke_method);
        } else {
            echo "Class $app does not exist.\n";
        }
    }
    private function process(CatEatCommand $command, string $invoke_method = '')
    {
        // 讀取文件內容
        $code = file_get_contents($this->app_path);

        // 解析現有的 PHP 文件
        $file = PhpFile::fromCode($code);

        // 獲取當前文件的命名空間
        $oldNamespace = $file->getNamespaces()['CatFoods'] ?? null;

        if ($oldNamespace) {
            // 創建一個新的命名空間
            $newNamespace = new PhpNamespace(name: 'Cat\Helpers\\' . ucfirst($this->tag));
            $newNamespace->addUse('Cat\Contracts\HelperContract');
            $newNamespace->addUse('Cat\Helpers\HelperTrait');

            // 將原有命名空間中的類和其他元素移動到新命名空間
            foreach ($oldNamespace->getClasses() as $className => $classObj) {
                unset($oldNamespace->getClasses()[$className]);
                $classObj->setImplements(['Cat\Contracts\HelperContract']);
                $classObj->addTrait('Cat\Helpers\HelperTrait');
                $classObj->addComment($this->app_title);
                $classObj->addComment($this->app_description);

                $classObj->addMethod('title')
                    ->setPublic()
                    ->setReturnType('string')
                    ->setBody('return \'' . $this->app_title . '\';');

                $classObj->addMethod('description')
                    ->setPublic()
                    ->setReturnType('string')
                    ->setBody('return \'' . $this->app_description . '\';');

                if (strlen($invoke_method) > 0) {
                    $classObj->addMethod('__invoke')
                        ->setPublic()
                        ->setBody('return $this->' . $invoke_method . '($argument);')
                        ->addParameter('argument');
                }
                // 將類移動到新的命名空間
                $newNamespace->add($classObj);
            }
            // 將新命名空間添加到文件
            $file->addNamespace($newNamespace);
        }
        // 刪除舊的命名空間（可選）
        unset($file->getNamespaces()['CatFoods']);

        // 重新生成php代碼
        $this->writeNewFile($command, $newNamespace);
    }


    private function writeNewFile(CatEatCommand $command, PhpNamespace $newNamespace)
    {
        $file = PhpFile::fromCode('<?php');
        // 將新命名空間添加到文件
        $file->addNamespace($newNamespace);

        // 重新生成修改後的 PHP 代碼
        $printer = new Printer();
        $modifiedCode = $printer->printFile($file);

        $writeFile = base_path('vendor/zhyu/cat/src/helpers/' . ucfirst($this->tag) . '/' . $this->className . '.php');
        if (file_exists($modifiedCode)) {
            if (!$command->confirm('Do you want to overwite exist file?', false)) {
                return 0;
            }
        }
        file_put_contents($writeFile, $modifiedCode);
        $this->writeConfig();
    }

    private function writeConfig()
    {
        $tag = ucfirst($this->tag);

        $configFile = base_path('vendor/zhyu/cat/config/macros.php');
        $cat_config = Config::get('zhyu.cat.macros', []);

        $className = $this->className;
//        if(isset($cat_config['macros'][lcfirst($className)])){
//            $this->error('此');
//            return 0;
//        }

        $classStrings = [];
        if (isset($cat_config) && is_array($cat_config)) {
            foreach ($cat_config as $name => $macro) {
                $classStrings[] = $macro;
            }
        }
        $classString = "Cat\\Helpers\\$tag\\$className";
        $new_macro = [
            lcfirst($className) => $classString,
        ];
        $classStrings[] = $classString;

        if (isset($cat_config) && is_array($cat_config)) {
            $cat_config = array_merge($cat_config, $new_macro);
        } else {
            $cat_config = $new_macro;
        }

        try {
            $config = ArrayFile::open($configFile);
            $config->set($cat_config);
            $configContent = $config->render();
            foreach ($classStrings as $string) {
                $escapedClassString = addslashes($string);
                $configContent = str_replace("'$escapedClassString'", "$string::class", $configContent);
            }
//            echo $configContent;
            file_put_contents($configFile, $configContent);
        } catch (\Exception $e) {
            dump($e);
        }
    }
}