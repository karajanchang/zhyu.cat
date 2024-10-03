<?php

namespace Cat\Console\Commands\CatEater;

use Cat\Console\Commands\CatEatCommand;
use Illuminate\Support\Facades\Config;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use Winter\LaravelConfigWriter\ArrayFile;

class TraitEater extends AbstractEater
{
    protected $src_directory = 'Traits';
    public function handle(CatEatCommand $command)
    {
        $this->getClassName();
        $app = $this->namespace . '\\' . $this->className;

        $reflection = new \ReflectionClass($app);
        echo "Trait name: " . $reflection->getName() . PHP_EOL;

        // 創建解析器
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

        try {
            $code = file_get_contents($this->app_path);
            // 解析代碼為 AST
            $ast = $parser->parse($code);
        } catch (Error $e) {
            echo 'Parse error: ', $e->getMessage();
            return;
        }

        // 創建節點遍歷器
        $traverser = new NodeTraverser();

        // 自定義節點訪問器，尋找 showMyIp 函數並添加註解
        $traverser->addVisitor(new class($this->app_name, $this->app_title, $this->app_description) extends NodeVisitorAbstract {
            private $app_name;
            private $app_title;
            private $app_description;

            // 在構造函數中接收變量
            public function __construct($app_name, $app_title, $app_description) {
                $this->app_name = $app_name;
                $this->app_title = $app_title;
                $this->app_description = $app_description;
            }

            public function enterNode(Node $node)
            {
                if ($node instanceof Node\Stmt\Trait_) {
                    $traitName = $node->name->toString();
                    // 添加註解
                    if($traitName===ucfirst($this->app_name)) {
                        $node->setDocComment(new \PhpParser\Comment\Doc("/**\n * " . $this->app_title . "\n *\n * " . $this->app_description . "\n*/"));
                    }
                }
            }
        });

        // 遍歷 AST 並修改
        $ast = $traverser->traverse($ast);

        // 重新生成修改後的 PHP 代碼
        $prettyPrinter = new PrettyPrinter\Standard();
        $updatedCode = $prettyPrinter->prettyPrintFile($ast);
//        echo $updatedCode;
        $writeFile = base_path('vendor/zhyu/cat/src/Traits/' . ucfirst($this->tag) . '/' . ucfirst($this->app_name) . '.php');
        if (file_exists($writeFile)) {
            if (!$command->confirm('Do you want to overwite exist file?', false)) {
                return 0;
            }
        }
        $updatedCode = preg_replace(
            '/namespace\s+CatFoods;/',
            'namespace Cat\Traits\\'.ucfirst($this->tag).';',
            $updatedCode
        );
//        echo $updatedCode;
        file_put_contents($writeFile, $updatedCode);
        $this->writeConfig();
    }

    private function writeConfig()
    {
        $configFile = base_path('vendor/zhyu/cat/config/traits.php');
        $cat_config = Config::get('zhyu.cat.traits', []);

        $new_trait[$this->tag][] = ucfirst($this->app_name);

        if (isset($cat_config) && is_array($cat_config)) {
            $cat_config = array_merge($cat_config, $new_trait);
        } else {
            $cat_config = $new_trait;
        }

        try {
            $config = ArrayFile::open($configFile);
            $config->set($cat_config);
            $configContent = $config->render();
            //echo $configContent;
            file_put_contents($configFile, $configContent);
        } catch (\Exception $e) {
            dump($e);
        }
    }
}