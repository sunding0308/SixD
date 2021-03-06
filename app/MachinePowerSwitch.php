<?php

namespace App;

class MachinePowerSwitch extends Machine
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Machines';

    public function __construct(array $attributes = [])
    {
        $attributes['type'] = self::TYPE_POWER_SWITCH;
        parent::__construct($attributes);
    }

    /**
     * Get a new query builder for the model's table.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery()
    {
        $builder = parent::newQuery();

        $builder->where('type', '=', self::TYPE_POWER_SWITCH);

        return $builder;
    }

    public function data()
    {
        return $this->hasOne(MachineDataPowerSwitch::class, 'machine_id','id');
    }
}
