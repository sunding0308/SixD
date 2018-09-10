<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    //product code
    const CODE_HOT_WATER = '001';
    const CODE_COLD_WATER = '002';
    const CODE_AIR = '003';
    const CODE_OXYGEN = '008';
    const CODE_HUMIDIFICATION = '005';
    const CODE_DEHUMIDIFICATION = '004';
    const CODE_CHILD_CONSTANT_HUMIDITY = '006';
    const CODE_ADULT_CONSTANT_HUMIDITY = '007';

    //water flow (ml)
    const HOT_WATER_FLOW = 450;
    const COLD_WATER_FLOW = 1000;

    //signal
    const SIGNAL_TOPUP = 'topup';
    const SIGNAL_VIP_TOPUP = 'vip_topup';
    const SIGNAL_RESET = 'reset';
    const SIGNAL_OVERAGE = 'overage';
    const SIGNAL_HARDWARE_STATUS = 'hardware_status';
    const SIGNAL_RECORDS = 'records';
    const SIGNAL_ENVIROMENT = 'environment';
    const SIGNAL_WATER_QUALITY_STATISTICS = 'water_quality_statistics';
    const SIGNAL_REDPACKET = 'redpacket';
    const SIGNAL_REDPACKET_RECEIVED = 'redpacket_received';
    const SIGNAL_ACCOUT_TYPE = 'account_type';
    const SIGNAL_APP_MENU_ANALYSIS = 'app_menu_analysis';
    const SIGNAL_API_ANALYSIS = 'api_analysis';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device', 'machine_id', 'registration_id', 'status', 'hot_water_overage', 'cold_water_overage', 'oxygen_overage', 'air_overage', 'humidity_add_overage', 
        'humidity_minus_overage', 'humidity_child_overage', 'humidity_adult_overage', 'filter1_lifespan', 'filter2_lifespan',
        'filter3_lifespan', 'g_status', 'wifi_status', 'bluetooth_status', 'temperature', 'humidity', 'pm2_5', 'oxygen_concentration', 'total_produce_water_time',
    ];

    public function bluetoothRecords()
    {
        return $this->hasMany(BluetoothRecord::class);
    }

    public function sterilization()
    {
        return $this->hasOne(Sterilization::class);
    }

    public function installation()
    {
        return $this->hasOne(Installation::class);
    }

    public function waterRecords()
    {
        return $this->hasMany(WaterRecord::class);
    }

    public function airRecords()
    {
        return $this->hasMany(AirRecord::class);
    }

    public function oxygenRecords()
    {
        return $this->hasMany(OxygenRecord::class);
    }

    public function humidityRecords()
    {
        return $this->hasMany(HumidityRecord::class);
    }

    public function waterQualityStatistics()
    {
        return $this->hasMany(WaterQualityStatistics::class);
    }

    public function alarm()
    {
        return $this->hasOne(Alarm::class);
    }

    public function hasAlarms()
    {
        if(!optional($this->alarm)->position_change_alarm
            && !optional($this->alarm)->service_alarm_status
            && !optional($this->alarm)->sterilization_alarm
            && !optional($this->alarm)->filter_alarm
            && !optional($this->alarm)->water_shortage_alarm
            && !optional($this->alarm)->filter_anti_counterfeiting_alarm
            && !optional($this->alarm)->slave_mobile_alarm
            && !optional($this->alarm)->dehumidification_tank_full_water_alarm
            && !optional($this->alarm)->malfunction_code
        ){
            return false;
        }

        return true;
    }
}
