<?php

declare (strict_types=1);
namespace App\Model;

/**
 * @property int $areaId 
 * @property int $cityId 城市ID
 * @property string $areaName 地区名称
 */
class CityArea extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'city_areas';
    protected $primaryKey = 'areaId';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['areaId', 'cityId', 'areaName'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['areaId' => 'integer', 'cityId' => 'integer'];
}