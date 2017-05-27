> 最近对接了几家流量平台，封装成composer包，改改配置，在其他项目也能用了，一劳永逸，何乐不为

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
# 未来无线
TRAFFIC_AGENT_ID=
TRAFFIC_APP_KEY=
TRAFFIC_APP_SECRET=
TRAFFIC_ORDER_AGENT_BACK_URL=

# 优比格流量接口
TRAFFIC_CHANNEL_CODE=
TRAFFIC_KEY=
```

## 用法

### 创建回调路由

未来无线回调示例

```php
//  一定要用 post 方法
//  一定要返回 succes 7个字符串
Route::post('traffic/callback', function () {
  $res = file_get_contents("php://input");
  Log::useFiles(storage_path('logs/traffic.log'));
  Log::info($res);
  return 'success';
});
```

回调地址需提供给平台，优比格回调路由示例

```php
// 优比格回调
Route::get('traffic/callback', function () {
  Log::useFiles(storage_path('logs/traffic.log'));
  Log::info(json_encode(app()->make('request')->all()));
  $result = [
    'code' => 1,
    'text' => 'success',
    'ext'  => []
  ];
  return response()->json($result);
});
```

### 内置方法

- 创建订单

```php
// $trafficSize为流量大小，int，单位为M兆
Traffic::submit($mobile, $trafficSize);
// 举例
Traffic::submit('158xxxxxxxx', 10);
```

- 查询渠道商余额

```php
Traffic::balance();
```

- 获取手机号运营商

```php
Traffic::getCarrier($mobile);
// 举例
Traffic::getCarrier('158xxxxxxxx');
```

### 代码示例

```php
// 查询余额
Route::get('/traffic/balance', function () {
    try {
        return Traffic::balance();
    } catch (\Beaplat\Traffic\Exceptions\TrafficException $e) {
        return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
    }
});

// 充值
Route::get('/traffic/submit', function () {
    try {
        $mobile = '158xxxxxxxx'; // 手机号
        $size = 10; // 流量大小 单位为M
        Traffic::submit($mobile, $size);
        return response()->json(['message' => '恭喜你充值成功，注意查收短信']);
    } catch (\Beaplat\Traffic\Exceptions\TrafficException $e) {
        return response()->json(['message' => $e->getMessage()]);
    }
});
```
