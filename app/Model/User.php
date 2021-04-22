<?php

declare (strict_types=1);
namespace App\Model;

/**
 * @property int $uid 
 * @property string $nickname 昵称
 * @property string $headimgurl 头像
 * @property string $openid openid
 * @property string $mini_openid 小程序openid
 * @property string $unionid unionid
 * @property int $wx_id 公众号ID
 * @property int $accounts_id 关联的账户Id
 */
class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';
    /**
     * 主键
     * @author: DHF 2021/4/13 14:36
     * @var string
     */
    protected $primaryKey = 'uid';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['uid', 'nickname', 'headimgurl', 'openid', 'mini_openid', 'unionid', 'wx_id', 'accounts_id'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['uid' => 'integer', 'wx_id' => 'integer', 'accounts_id' => 'integer'];
}