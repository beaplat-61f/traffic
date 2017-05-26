## Installation

```
composer require beaplat/traffic
```

Open your `config/app.php` and add the following to the `providers` array

```
Beaplat\Traffic\TrafficProvider::class,
```

In the same `config/app.php` and add the following to the `aliases` array

```
'Traffic'   => Beaplat\Traffic\Facades\Traffic::class,
```

```
php artisan vendor:publish --provider="Beaplat\Traffic\TrafficProvider"
```

It will generate a config file `traffic.php` under the path `config` and a migration file under the path `database/migrations`

```
php artisan migrate
```

.env

```
# 未来无线
TRAFFIC_AGENT_ID=
TRAFFIC_APP_KEY=
TRAFFIC_APP_SECRET=
TRAFFIC_ORDER_AGENT_BACK_URL=

# 优比格流量接口
TRAFFIC_CHANNEL_CODE=
TRAFFIC_KEY=
```

## Using

> 注意：一定要用post，一定要return success
Route::post('traffic/callback', function () {
  $res = file_get_contents("php://input");
  Log::useFiles(storage_path('logs/traffic.log'));
  Log::info($res);
  //$order = json_decode($res, true);
// Traffic::updateOrder($order['order_agent_bill'], $order);
//file_put_contents("callback.txt", $res . PHP_EOL, FILE_APPEND);
  return 'success';
});

### 充值流量

```php
try {
        // 手机号
        $mobile = '158xxxxxxxx';
        // 流量大小 单位为M
        $size = 10;
        Traffic::submit($mobile, $size);
        return response()->json(['message' => '恭喜你充值成功，注意查收短信']);
    } catch (\Beaplat\Traffic\Exceptions\TrafficException $e) {
        return response()->json(['message' => $e->getMessage()]);
    }
```

### 查询余额

```php
try {
    return Traffic::balance();
} catch (\Beaplat\Traffic\Exceptions\TrafficException $e) {
    return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
}
```