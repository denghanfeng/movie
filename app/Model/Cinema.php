<?php

declare (strict_types=1);
namespace App\Model;

/**
 * @property int $cinemaId 
 * @property int $cityId 城市ID
 * @property string $cinemaName 影院名称
 * @property string $address 影院地址
 * @property string $latitude 纬度
 * @property string $longitude 经度
 * @property string $phone 影院电话
 * @property string $regionName 地区名称
 * @property int $areaId 地区ID
 * @property int $isAcceptSoonOrder 是否支持秒出票，0为不支持，1为支持
 */
class Cinema extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cinemas';
    protected $primaryKey = 'cinemaId';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['cinemaId', 'cityId', 'cinemaName', 'address', 'latitude', 'longitude', 'phone', 'regionName', 'areaId', 'isAcceptSoonOrder'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['cinemaId' => 'integer', 'cityId' => 'integer', 'areaId' => 'integer', 'isAcceptSoonOrder' => 'integer'];
}