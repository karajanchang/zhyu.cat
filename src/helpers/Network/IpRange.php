<?php

namespace Cat\Helpers\Network;

use Cat\Contracts\HelperContract;
use Cat\Helpers\HelperTrait;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\IpUtils;

class IpRange implements HelperContract
{
    use HelperTrait;
    public function title(): string
    {
       return 'IP範圍的檢查';
    }

    public function description(): string
    {
        return '可以用192.168.0.*或192.168.0.0/24來檢查IP是否在範圍內';
    }

    public function __invoke($argument)
    {
       return $this->check($argument);
    }
    public function check($argument){
//        dump(__METHOD__);
        $whiteIpList = explode(',', env('WHITE_IP_LIST', '203.74.114.*,59.124.13.*'));

        foreach ($whiteIpList as $range) {
            // 檢查精確匹配
            if ($range === $argument) {
                return true;
            }

            // 檢查通配符模式
            if (Str::contains($range, '*')) {
                $pattern = str_replace(['*'], ['*'], $range);
                // 確保 $pattern 是字串
                if (Str::is($pattern, $argument)) {
                    return true;
                }
            }
            // 檢查CIDR標記
            elseif (strpos($range, '/') !== false) {
                if (IpUtils::checkIp($argument, $range)) {
                    return true;
                }
            }
        }

        return false;
    }
}