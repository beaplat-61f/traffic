<?php
return [
    'base_url' => env('TRAFFIC_BASE_URL', 'http://120.24.173.64:7001/index.do'),

    'agent_id' => env('TRAFFIC_AGENT_ID', ''),

    'app_key' => env('TRAFFIC_APP_KEY', ''),

    'app_secret' => env('TRAFFIC_APP_SECRET', ''),

    // 回调地址
    'order_agent_back_url' => env('TRAFFIC_ORDER_AGENT_BACK_URL', ''),

    // 如果该参数为空或者为all时，走全国通道
    // 如果该参数是某省份拼音时，走分省通道
    'traffic_usage_province' => env('TRAFFIC_AGENT_USAGE_PROVINCE', 'all'),

    // 全国三网流量的标准包 单位为M
    'traffic_carrier' => [
        // 联通
        'unicom'  => [20, 50, 100, 200, 500],

        'telecom' => [5, 10, 30, 50, 100, 200, 500, 1024],

        'mobile'  => [10, 30, 70, 150, 500, 1024, 2*1024, 3*1024, 4*1024, 6*1024, 11*1024]
    ],
];
