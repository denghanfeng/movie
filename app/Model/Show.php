<?php

declare (strict_types=1);
namespace App\Model;

/**
 * @property string $showId 场次标识
 * @property int $cinemaId 影院id
 * @property string $hallName 影厅名
 * @property int $filmId 影片id
 * @property string $filmName 影片名字
 * @property int $duration 时长,分钟
 * @property string $showTime 放映时间
 * @property string $stopSellTime 停售时间
 * @property string $showVersionType 场次类型
 * @property int $netPrice 参考价，单位：分
 * @property string $language 语言
 * @property string $planType 影厅类型 2D 3D
 * @property string $created_at 
 * @property string $updated_at 
 */
class Show extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shows';
    protected $primaryKey = 'showId';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['showId', 'cinemaId', 'hallName', 'filmId', 'filmName', 'duration', 'showTime', 'stopSellTime', 'showVersionType', 'netPrice', 'language', 'planType', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['cinemaId' => 'integer', 'filmId' => 'integer', 'duration' => 'integer', 'netPrice' => 'integer'];
}