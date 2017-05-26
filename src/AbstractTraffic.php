<?php

namespace Beaplat\Traffic;

use Beaplat\Traffic\Exceptions\TrafficException;

abstract class AbstractTraffic
{
    /**
     * 充值订单
     *
     * @param string $mobile 手机号
     * @param int $size 流量大小 单位为M
     *
     * @return mixed
     */
    abstract public function submit($mobile, $size);

//    public function status();

    /**
     * 查询余额
     *
     * @return mixed
     */
    abstract public function balance();

    /**
     * 获取手机运营商
     *
     * @param $mobile
     * @return mixed
     */
    public function getCarrier($mobile)
    {
        if (preg_match('/^1(3[4-9]|47|5[0-17-9]|78|8[2-47-8])[\d]{8}$/', $mobile)) {
            // 移动
            return 'mobile';
        } elseif (preg_match('/^1(3[0-2]|45|5[56]|76|8[56])[\d]{8}$/', $mobile)) {
            // 联通
            return 'unicom';
        } elseif (preg_match('/^1(33|53|77|8[019])[\d]{8}$/', $mobile)) {
            // 电信
            return 'telecom';
        } else {
            throw new TrafficException("Invalid mobile [$mobile]");
        }
    }
}
