<?php

declare (strict_types=1);
namespace App\Model;

/**
 * @property int $filmId 影片id
 * @property int $grade 评分
 * @property string $name 影片名
 * @property int $duration 时长，分钟
 * @property string $publishDate 影片上映日期
 * @property string $director 导演
 * @property string $cast 主演
 * @property string $intro 简介
 * @property string $versionTypes 上映类型
 * @property string $language 语言
 * @property string $filmTypes 影片类型
 * @property string $pic 海报URL地址
 * @property string $like 想看人数
 * @property int $showStatus 放映状态：1 正在热映。2 即将上映
 */
class Filme extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'filmes';
    protected $primaryKey = 'filmId';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['filmId', 'grade', 'name', 'duration', 'publishDate', 'director', 'cast', 'intro', 'versionTypes', 'language', 'filmTypes', 'pic', 'like', 'showStatus'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['filmId' => 'integer', 'grade' => 'integer', 'duration' => 'integer', 'showStatus' => 'integer'];
}