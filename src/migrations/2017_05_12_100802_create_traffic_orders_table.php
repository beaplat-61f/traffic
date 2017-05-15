<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrafficOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traffic_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户ID');
            $table->string('order_system_bill')->comment('流量平台返回订单号');
            $table->string('order_agent_bill')->comment('自定义订单号');
            $table->index('order_agent_bill');
            $table->char('order_tel', 11)->comment('充值手机号');
            $table->integer('traffic_size')->comment('充值流量大小，单位M兆');
            $table->boolean('order_is_succeed')->default(0)->comment('订单是否充值成功');
            $table->dateTime('order_system_submit_time')->comment('流量平台提交订单时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('traffic_orders');
    }
}
