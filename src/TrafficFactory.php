<?php

namespace Beaplat\Traffic;


class TrafficFactory
{
    public static function getInstance($platform = 'u_bigger')
    {
        switch ($platform) {
            // 未来无线
            case 'future':
                return new FutureWireless();
                break;
            // 优比格
            case 'u_bigger':
                return new UBigger();
                break;
            default:
                break;
        }
    }
}