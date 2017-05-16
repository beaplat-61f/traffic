> 最近对接唯诚流量平台，封装成composer包，改改配置，在其他项目也能用了，一劳永逸，何乐不为

## 安装

```
composer require beaplat/traffic
```

打开文件 `config/app.php` 添加 `providers`

```
Beaplat\Traffic\TrafficProvider::class,
```

相同的文件添加 `aliases`

```
'Traffic'   => Beaplat\Traffic\Facades\Traffic::class,
```

生成配置文件和迁移

```
php artisan vendor:publish --provider="Beaplat\Traffic\TrafficProvider"
```

执行迁移文件

```
php artisan migrate
```

修改配置 `.env`

```
TRAFFIC_AGENT_ID=
TRAFFIC_APP_KEY=
TRAFFIC_APP_SECRET=
TRAFFIC_ORDER_AGENT_BACK_URL=
```

修改 **UTC** 时区为 **PRC**

```php
// config/app.php 
'timezone' => PRC
```

## 用法

### 创建回调路由

路由示例【待整理，代码乱七八糟】

```php
Route::post('traffic/callback', function () {
  $res = file_get_contents("php://input");
  Log::useFiles(storage_path('logs/traffic.log'));
  Log::info($res);
  //$order = json_decode($res, true);
// Traffic::updateOrder($order['order_agent_bill'], $order);
//file_put_contents("callback.txt", $res . PHP_EOL, FILE_APPEND);
  return 'success';
});
```

> - 一定要用 `post` 方法
> - 一定要返回 `success` 7个字符串

### 内置方法

- 创建订单

```php
// $trafficSize为流量大小，int，单位为M兆
Traffic::submit($userId, $mobile, $trafficSize);
// 举例
Traffic::submit(1, '15820156666', 10);
```

- 查询渠道商余额

```php
Traffic::balance();
```

- 查询订单状态

```php
Traffic::getStatus($orderSystemBill, $orderAgentBill);
// 举例
Traffic::getStatus('WT494917048893888505', '201705161494917016503159045065');
```

- 检测全国三网流量的标准包

```php
// $carrier为手机运营商，可选值为'unicom'（联通），'telecom'（电信），'mobile'（移动）
Traffic::checkTrafficValid($carrier, $size);
// 举例
Traffic::checkTrafficValid('mobile', 10);
```

- 获取手机号运营商

```php
Traffic::getCarrier($mobile);
// 举例
Traffic::getCarrier('15820156666');
```
