<?php

namespace Beaplat\Traffic;

use GuzzleHttp\Client;
use Beaplat\Traffic\Exceptions\TrafficException;

class TrafficHelper
{
    public function __construct()
    {
        $this->client = new Client();

        $this->base_url = config('traffic.base_url');

        $this->agent_id = config('traffic.agent_id');

        $this->app_key = config('traffic.app_key');

        $this->app_secret = config('traffic.app_secret');

        $this->order_agent_back_url = config('traffic.order_agent_back_url');

        $this->traffic_usage_province = config('traffic.traffic_usage_province');

        $this->traffic_carrier = config('traffic.traffic_carrier');
    }

    /**
     * 提交流量充值订单
     *
     * @param integer $userId
     * @param string  $mobile
     * @param integer $size
     *
     * @return mixed
     */
    public function submit($userId, $mobile, $size)
    {
        try {
            if (! $this->checkTrafficValid($this->getCarrier($mobile), $size)) {
                throw new TrafficException('Can not find the prize of the carrier');
            }
        } catch (TrafficException $e) {
            throw new TrafficException($e->getMessage(), $e->getCode());
        }

        $timestamp = time() * 1000;
        $orderAgentBill = $this->createOrderAgentBill();
        $response = $this->client->request('GET', $this->base_url, [
            'query' => [
                'action' => 'api_order_traffic_submit',
                'app_key' => $this->app_key,
                'order_agent_bill' => $orderAgentBill,
                'order_agent_id' => $this->agent_id,
                'order_agent_back_url' => urlencode($this->order_agent_back_url),
                'order_tel' => $mobile,
                'traffic_size' => $size,
                'timestamp' => $timestamp,
                'traffic_usage_province' => $this->traffic_usage_province,
                'app_sign' => strtoupper(md5($this->app_key . $this->app_secret . $this->agent_id . $timestamp . $orderAgentBill . $mobile))
            ]
        ]);
        $contents = json_decode($response->getBody()->getContents());
        if ($contents->code === '0000') {
            return TrafficOrder::create([
                'user_id' => $userId,
                'order_agent_bill' => $orderAgentBill,
                'order_tel' => $mobile,
                'traffic_size' => $size
            ]);
        } else {
//            throw new TrafficException('Create order fail, error message: ' . $contents->msg . ', error code: ' . $contents->code);
            throw new TrafficException($contents->msg, $contents->code);
        }
    }

    /**
     * 查询渠道商余额
     *
     * @return mixed
     */
    public function balance()
    {
        $timestamp = time() * 1000;
        $response = $this->client->request('GET', $this->base_url, [
            'query' => [
                'action' => 'api_agent_query_balance',
                'app_key' => $this->app_key,
                'agent_id' => $this->agent_id,
                'timestamp' => $timestamp,
                'app_sign' => strtoupper(md5($this->app_key . $this->app_secret . $this->agent_id . $timestamp))
            ]
        ]);
        $contents = json_decode($response->getBody()->getContents());

        if ($contents->code === '0000') {
            return $contents->object->agent_balance;
        } else {
            throw new TrafficException($contents->msg, $contents->code);
        }
    }

    /**
     * 查询订单状态
     *
     * @param $orderSystemBill
     * @param $orderAgentBill
     *
     * @return mixed
     */
    public function getStatus($orderSystemBill, $orderAgentBill)
    {
        $timestamp = time() * 1000;
        $response = $this->client->request('GET', $this->base_url, [
            'query' => [
                'action' => 'api_order_traffic_query',
                'app_key' => $this->app_key,
                'agent_id' => $this->agent_id,
                'order_agent_bill' => $orderAgentBill,
                'order_system_bill' => $orderSystemBill,
                'timestamp' => $timestamp,
                'app_sign' => strtoupper(md5($this->app_key . $this->app_secret . $this->agent_id . $timestamp))
            ]
        ]);
        $contents = json_decode($response->getBody()->getContents());

        if ($contents->code === '0000') {
            return $contents->object;
        } else {
//            throw new TrafficException('查询状态失败，错误信息：' . $contents->msg . '错误码：' . $contents->code);
            throw new TrafficException($contents->msg, $contents->code);
        }
    }

    public function createOrderAgentBill()
    {
        return date('Ymd', time()) . str_pad(str_replace('.','',microtime(true)),14,'X') . mt_rand(10000000,99999999);
    }

    /**
     * 检测全国三网流量的标准包
     *
     * @param string  $carrier
     * @param integer $size
     *
     * @return bool
     */
    public function checkTrafficValid($carrier, $size)
    {
        if (! in_array($carrier, ['unicom', 'telecom', 'mobile'])) {
            throw new TrafficException('Invalid carrier');
        }
        if (in_array($size, $this->traffic_carrier[$carrier])) {
            return true;
        }
        return false;
    }


    /**
     * 获取手机号运营商
     *
     * @param string $mobile
     *
     * @return mixed
     */
    public function getCarrier($mobile)
    {
        $timestamp = time() * 1000;
        $response = $this->client->request('GET', $this->base_url, [
            'query' => [
                'action' => 'api_query_tel_info',
                'app_key' => $this->app_key,
                'agent_id' => $this->agent_id,
                'query_tel' => $mobile,
                'timestamp' => $timestamp,
                'app_sign' => strtoupper(md5($this->app_key . $this->app_secret . $this->agent_id . $timestamp))
            ]
        ]);
        $contents = json_decode($response->getBody()->getContents());
        if ($contents->code === '0000') {
            return $contents->object->carrier;
        } else {
//            throw new TrafficException('查询失败，错误信息：' . $contents->msg . '错误码：' . $contents->code);
            throw new TrafficException($contents->msg, $contents->code);
        }
    }

    /**
     * 回调更新订单
     *
     * @param $orderAgentBill
     * @param $data
     *
     * @return mixed
     */
    public function updateOrder($orderAgentBill, $data)
    {
        $order = TrafficOrder::where('orderAgentBill', $orderAgentBill);
        if ($order) {
            $order->order_system_bill = $data['order_system_bill'];
            $order->order_is_succeed = $data['order_is_succeed'];
            // config/app.php timezone => PRC
            $order->order_system_submit_time = date('Y-m-d H:i:s', intval($data['order_system_submit_time'] / 1000));
            $order->save();
            return $order;
        } else {
            throw new TrafficException('Can not find the order with agent bill:' . $orderAgentBill);
        }
    }
}
