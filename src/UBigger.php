<?php

namespace Beaplat\Traffic;

use Beaplat\Traffic\Exceptions\TrafficException;
use GuzzleHttp\Client;

class UBigger extends AbstractTraffic
{
    private $client;

    private $base_url;

    private $channel_code;

    private $key;

    public function __construct()
    {
        $this->client = new Client();

        $this->base_url = 'http://uumon.com/api/v2/egame/flow/package';

        $platformConfig = config('traffic.platform.u_bigger');

        $this->channel_code = $platformConfig['channel_code'];

        $this->key = $platformConfig['key'];
    }

    public function submit($mobile, $size)
    {
        $timestamp = time() * 1000;
        $reqId = $this->createReqId();
        try {
            $pkgId = $this->getPkgId($this->getCarrier($mobile), $size);
            $response = $this->client->request('GET', $this->base_url . '/order.json', [
                'query' => [
                    'req_id'       => $reqId,
                    'phone'        => $mobile,
                    'pkg_id'       => $pkgId,
                    'pkg_name'     => $size . 'M',
                    'channel_code' => $this->channel_code,
                    'timestamp'    => $timestamp,
                    'md5'          => md5($reqId . $mobile . $pkgId . $this->channel_code . $timestamp . $this->key)
                ]
            ]);
            $contents = json_decode($response->getBody()->getContents());
            if ($contents->code === 0) {
                return $contents;
            } else {
                throw new TrafficException($contents->text, $contents->code);
            }
        } catch (TrafficException $e) {
            throw new TrafficException($e->getMessage());
        }

    }

    /**
     * 查询余额
     *
     * @return mixed
     */
    public function balance()
    {
        $response = $this->client->request('GET', $this->base_url . '/balance.json', [
            'query' => [
                'channel_code' => $this->channel_code,
            ]
        ]);
        $contents = json_decode($response->getBody()->getContents());
        if ($contents->code === 0) {
            return $contents->ext;
        } else {
            throw new TrafficException($contents->text, $contents->code);
        }
    }

    /**
     * 创建随机的20位订单编号
     *
     * @return string
     */
    public function createReqId()
    {
        return date('YmdHis') . mt_rand(100000, 999999);
    }

    /**
     * 获取销售品id
     *
     * @param string $carrier 运营商
     * @param int    $size    流量大小
     *
     * @return mixed
     */
    public function getPkgId($carrier, $size)
    {
        $productIdConfig = config('traffic.platform.u_bigger.product_id');
        if (! array_key_exists($carrier, $productIdConfig)) {
            throw new TrafficException("Undefined carrier [$carrier]");
        }
        $carrierConfig = array_get($productIdConfig, $carrier);
        if (! array_key_exists($size, $carrierConfig)) {
            throw new TrafficException("Can not find the size [$size] of the carrier [$carrier]");
        }
        return array_get($carrierConfig, $size);
    }

    /**
     * 查询订单接口
     *
     * @param string $reqId
     * @param string $mobile
     *
     * @return mixed
     */
    public function findOrder($reqId, $mobile)
    {
        $response = $this->client->request('GET', $this->base_url . '/query_order.json', [
            'query' => [
                'req_id'       => $reqId,
                'phone'        => $mobile,
                'channel_code' => $this->channel_code,
            ]
        ]);
        $contents = json_decode($response->getBody()->getContents(), true);
        return $contents;
    }
}
