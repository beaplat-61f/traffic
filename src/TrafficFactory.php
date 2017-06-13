<?php

namespace Beaplat\Traffic;


class TrafficFactory
{
    public static function getInstance($platform = 'u_bigger')
    {
        /*switch ($platform) {
            // 未来无线
            case 'future_wireless':
                return new FutureWireless();
                break;
            // 优比格
            case 'u_bigger':
                return new UBigger();
                break;
            default:
                break;
        }*/
        // 更加简练的写法
        $class = ucfirst(camel_case($platform));
        return new $class;
    }
}