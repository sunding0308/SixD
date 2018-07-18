<?php

use App\User;
use App\Alarm;
use App\Machine;
use App\UserRank;
use App\Sterilization;
use App\WaterQualityStatistics;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataInitialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();
        User::create([
            'name' => 'admin',
            'email' => 'admin@6d.com',
            'password' => Hash::make('ABCabc01')
        ]);
        // DB::table('machines')->truncate();
        // $machine = Machine::create([
        //     'device' => 'f4:0f:24:15:9b:0a',
        //     'registration_id' => '140fe1da9efb58700ab',
        //     'status' => '当前正处于睡眠状态',
        //     'g_status' => '信号强',
        //     'wifi_status' => '未连接',
        //     'bluetooth_status' => '当前未连接',
        //     'hot_water_overage' => 3600,
        //     'cold_water_overage' => 3600,
        //     'oxygen_overage' => 0,
        //     'air_overage' => 0,
        //     'humidity_overage' => 0,
        //     'filter1_lifespan' => '2200h,0%，滤芯待更换',
        //     'filter2_lifespan' => '300h,70%',
        //     'filter3_lifespan' => '300h,70%',
        //     'temperature' => 25,
        //     'humidity' => 75,
        //     'pm2_5' => 20,
        //     'oxygen_concentration' => 95,
        //     'total_produce_water_time' => 7200
        // ]);
        // DB::table('sterilizations')->truncate();
        // Sterilization::create([
        //     'machine_id' => $machine->id,
        //     'uv1' => 0,
        //     'uv2' => 0,
        //     'uv3' => 0,
        //     'uv4' => 0,
        //     'uv5' => 0,
        //     'uv6' => 0
        // ]);
        // DB::table('alarms')->truncate();
        // Alarm::create([
        //     'machine_id' => $machine->id,
        //     'position_change_alarm' => '当前定位为广东省，安装位置为上海市',
        //     'service_alarm_status' => '机器处于待加水状态',
        //     'sterilization_alarm' => 'UV1，开启总时间已到达2000h',
        //     'filter_alarm' => '二级滤芯2年未更换',
        //     'water_shortage_alarm' => '',
        //     'filter_anti_counterfeiting_alarm' => '',
        //     'slave_mobile_alarm' => '',
        //     'dehumidification_tank_full_water_alarm' => '',
        //     'malfunction_code' => 'E1',
        // ]);
        // DB::table('water_quality_statistics')->truncate();
        // DB::table('water_quality_statistics')->insert([
        //     ['machine_id' => $machine->id, 'raw_water_tds' => 999, 'pure_water_tds' => 5, 'salt_rejection_rate' => 95, 'created_at' => '2018-06-05 08:05:23', 'updated_at' => '2018-06-05 08:05:23'],
        //     ['machine_id' => $machine->id, 'raw_water_tds' => 934, 'pure_water_tds' => 7, 'salt_rejection_rate' => 95, 'created_at' => '2018-06-06 08:05:23', 'updated_at' => '2018-06-06 08:05:23'],
        //     ['machine_id' => $machine->id, 'raw_water_tds' => 954, 'pure_water_tds' => 6, 'salt_rejection_rate' => 94, 'created_at' => '2018-06-07 08:05:23', 'updated_at' => '2018-06-07 08:05:23'],
        //     ['machine_id' => $machine->id, 'raw_water_tds' => 945, 'pure_water_tds' => 3, 'salt_rejection_rate' => 93, 'created_at' => '2018-06-08 08:05:23', 'updated_at' => '2018-06-08 08:05:23'],
        //     ['machine_id' => $machine->id, 'raw_water_tds' => 923, 'pure_water_tds' => 5, 'salt_rejection_rate' => 93, 'created_at' => '2018-06-09 08:05:23', 'updated_at' => '2018-06-09 08:05:23'],
        //     ['machine_id' => $machine->id, 'raw_water_tds' => 934, 'pure_water_tds' => 7, 'salt_rejection_rate' => 93, 'created_at' => '2018-06-10 08:05:23', 'updated_at' => '2018-06-10 08:05:23'],
        //     ['machine_id' => $machine->id, 'raw_water_tds' => 999, 'pure_water_tds' => 12, 'salt_rejection_rate' => 92, 'created_at' => '2018-06-11 08:05:23', 'updated_at' => '2018-06-11 08:05:23'],
        //     ['machine_id' => $machine->id, 'raw_water_tds' => 955, 'pure_water_tds' => 16, 'salt_rejection_rate' => 92, 'created_at' => '2018-06-12 08:05:23', 'updated_at' => '2018-06-12 08:05:23'],
        //     ['machine_id' => $machine->id, 'raw_water_tds' => 967, 'pure_water_tds' => 3, 'salt_rejection_rate' => 91, 'created_at' => '2018-06-13 08:05:23', 'updated_at' => '2018-06-13 08:05:23'],
        //     ['machine_id' => $machine->id, 'raw_water_tds' => 945, 'pure_water_tds' => 5, 'salt_rejection_rate' => 91, 'created_at' => '2018-06-14 08:05:23', 'updated_at' => '2018-06-14 08:05:23'],
        //     ['machine_id' => $machine->id, 'raw_water_tds' => 923, 'pure_water_tds' => 4, 'salt_rejection_rate' => 91, 'created_at' => '2018-06-15 08:05:23', 'updated_at' => '2018-06-15 08:05:23']
        // ]);
        // DB::table('bluetooth_records')->truncate();
        // DB::table('bluetooth_records')->insert([
        //     ['machine_id' => $machine->id, 'started_at' => '2018-06-05 08:05:23', 'stopped_at' => '2018-06-05 10:05:23', 'total_time' => 7200, 'created_at' => '2018-06-05 12:05:23', 'updated_at' => '2018-06-05 12:05:23'],
        //     ['machine_id' => $machine->id, 'started_at' => '2018-06-05 11:02:12', 'stopped_at' => '2018-06-05 12:05:23', 'total_time' => 3600, 'created_at' => '2018-06-05 12:05:23', 'updated_at' => '2018-06-05 12:05:23']
        // ]);
        // DB::table('water_records')->truncate();
        // DB::table('water_records')->insert([
        //     ['machine_id' => $machine->id, 'date' => '2018-06-05 08:05:23', 'time' => 60, 'flow' => 450, 'total_flow' => 450],
        //     ['machine_id' => $machine->id, 'date' => '2018-06-05 08:21:00', 'time' => 30, 'flow' => 1000, 'total_flow' => 500]
        // ]);
        // DB::table('oxygen_records')->truncate();
        // DB::table('oxygen_records')->insert([
        //     ['machine_id' => $machine->id, 'date' => '2018-06-05 08:05:23', 'time' => 7200],
        //     ['machine_id' => $machine->id, 'date' => '2018-06-05 08:21:00', 'time' => 3600]
        // ]);
        // DB::table('air_records')->truncate();
        // DB::table('air_records')->insert([
        //     ['machine_id' => $machine->id, 'date' => '2018-06-05 08:05:23', 'time' => 7200],
        //     ['machine_id' => $machine->id, 'date' => '2018-06-05 08:21:00', 'time' => 3600]
        // ]);
        // DB::table('humidity_records')->truncate();
        // DB::table('humidity_records')->insert([
        //     ['machine_id' => $machine->id, 'type' => '加湿', 'date' => '2018-06-05 08:05:23', 'time' => 7200],
        //     ['machine_id' => $machine->id, 'type' => '除湿', 'date' => '2018-06-05 08:21:00', 'time' => 3600]
        // ]);
        // DB::table('user_ranks')->truncate();
        // UserRank::create([
        //     'machine_id' => 1,
        //     'user_id' => 1001,
        //     'user_nickname' => '孤独风中一匹狼',
        //     'rank' => 123,
        //     'machine_rank' => 34
        // ]);
    }
}
