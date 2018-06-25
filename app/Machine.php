<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device', 'registration_id', 'status', 'water_overage', 'oxygen_overage', 'air_overage', 'humidity_overage', 'filter1_lifespan', 'filter2_lifespan', 'filter3_lifespan',
        'g_status', 'wifi_status', 'bluetooth_status', 'temperature', 'humidity', 'pm2_5', 'oxygen_concentration', 'total_produce_water_time',
    ];

    public function bluetoothRecords()
    {
        return $this->hasMany(BluetoothRecord::class);
    }

    public function sterilization()
    {
        return $this->hasOne(Sterilization::class);
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

    public function userRank()
    {
        return $this->hasOne(UserRank::class);
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
