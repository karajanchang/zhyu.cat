<?php

namespace Cat\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class CatInstallCommand extends Command
{
    protected $signature = 'cat:install {--rollback : 還原}';

    protected $description = '安裝composer.json中的catFoods和建立目錄';


    public function handle(){
        $composerFile = 'composer.json';
        $composerRollbackFile = 'composer.json.back';
        if (!file_exists($composerFile)) {
            $this->error("composer.json file not found.\n");
            return 0;
        }
        if(!file_exists($composerRollbackFile)) {
            copy($composerFile, $composerRollbackFile);
        }

        // 读取 composer.json 的内容
        $composerData = json_decode(file_get_contents($composerFile), true);

        // 确保 autoload 和 psr-4 部分存在
        $autoload = &$composerData['autoload'];
        if (!isset($autoload)) {
            $autoload = [];
        }

        $psr4 = &$autoload['psr-4'];
        if (!isset($psr4)) {
            $psr4 = [];
        }

        $path = Config::get('zhyu.cat.path', []);

        // 定义要添加的命名空间
        $namespace = ucfirst($path['catFoods']).'\\';
        $path = $path['catFoods'];

        if(!file_exists($path)){
            mkdir($path);
        }


        // 检查命名空间是否已经存在
        if (!array_key_exists($namespace, $psr4)) {
            // 添加新的 PSR-4 规则
            $psr4[$namespace] = $path;

            // 将修改后的数据写回 composer.json 文件
            file_put_contents($composerFile, json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
            $this->info('Cat has been install successfully.');
        } else {
            $this->info('Cat has been install.');
        }

        exec('composer dump-autoload', $output, $return_var);

        // 输出执行结果
        if ($return_var === 0) {
            $this->info('Composer dump-autoload executed successfully.');
        } else {
            $this->error('Error executing composer dump-autoload: ' . implode("\n", $output));
        }
    }
}