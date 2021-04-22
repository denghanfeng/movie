<?php

declare (strict_types=1);
namespace App\Model;

/**
 * @property int $cityId 
 * @property string $pinYin pinYin
 * @property string $regionName 城市名
 */
class City extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'citys';
    /**
     * 主键
     * @author: DHF 2021/4/13 17:21
     * @var string
     */
    protected $primaryKey = 'cityId';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['cityId', 'pinYin', 'regionName'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['cityId' => 'integer'];
}