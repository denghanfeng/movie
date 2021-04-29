<?php

declare (strict_types=1);
namespace App\Model;

/**
 * @property int $id 
 * @property string $action 事件
 * @property string $start_time 开始时间
 * @property string $end_time 结束时间
 * @property string $errorJson 错误信息
 */
class StakLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stak_log';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'action', 'start_time', 'end_time', 'errorJson'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer'];
}