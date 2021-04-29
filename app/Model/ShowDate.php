<?php

declare (strict_types=1);
namespace App\Model;

/**
 * @property int $id 
 * @property int $filmId 影片id
 * @property int $cinemaId 影院id
 * @property int $cityId 城市ID
 * @property string $date 城市ID
 */
class ShowDate extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'show_dates';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'filmId', 'cinemaId', 'cityId', 'date'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'filmId' => 'integer', 'cinemaId' => 'integer', 'cityId' => 'integer'];
    public $timestamps = false;
}