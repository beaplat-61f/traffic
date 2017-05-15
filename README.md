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
TRAFFIC_AGENT_ID=
TRAFFIC_APP_KEY=
TRAFFIC_APP_SECRET=
TRAFFIC_ORDER_AGENT_BACK_URL=
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