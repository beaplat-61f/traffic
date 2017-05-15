<?php

namespace Beaplat\Traffic;

use Illuminate\Database\Eloquent\Model;

class TrafficOrder extends Model
{
    protected $table = 'traffic_orders';

    protected $guarded = ['id'];
}