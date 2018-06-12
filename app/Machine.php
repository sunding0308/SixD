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
        'unique_code', 'registration_id', 'status', 'overage', 'filter1_lifespan', 'filter2_lifespan', 'filter3_lifespan',
        '2g4g_status', 'wifi_status', 'bluetooth_status', 'temperature', 'humidity', 'pm2_5', 'oxygen_concentration', 'total_produce_water_time',
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
}
