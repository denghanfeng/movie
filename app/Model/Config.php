<?php

declare (strict_types=1);
namespace App\Model;

/**
 * @property int $id 
 * @property string $namespace 空间
 * @property string $key key
 * @property string $value value
 */
class Config extends Model
{
    public $timestamps = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'configs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'namespace', 'key', 'value'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer'];
}