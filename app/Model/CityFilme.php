<?php

declare (strict_types=1);
namespace App\Model;

/**
 * @property int $id 
 * @property int $cityId 城市ID
 * @property int $filmId 影片id
 * @property-read \App\Model\Filme $filme 
 */
class CityFilme extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'city_filmes';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'cityId', 'filmId'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'cityId' => 'integer', 'filmId' => 'integer'];
    /**
     * 电影
     * @return \Hyperf\Database\Model\Relations\HasOne
     * @author: DHF 2021/4/28 17:05
     */
    public function filme()
    {
        return $this->hasOne(Filme::class, 'filmId', 'filmId');
    }
}