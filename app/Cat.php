<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $race_id
 * @property string $name
 * @property int $status
 * @property string $birth
 * @property string $last_vaccine
 * @property string $photos_1
 * @property string $photos_2
 * @property string $photos_3
 * @property string $photos_4
 * @property string $photos_5
 * @property string $created_at
 * @property string $updated_at
 * @property Race $race
 * @property User $user
 * @property Mating[] $matings
 * @property Mating[] $matings
 */
class Cat extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'race_id', 'name', 'status', 'birth', 'last_vaccine', 'photos_1', 'photos_2', 'photos_3', 'photos_4', 'photos_5', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function race()
    {
        return $this->belongsTo('App\Race');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function matings_1()
    {
        return $this->hasMany('App\Mating', 'cat_id_1');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function matings_2()
    {
        return $this->hasMany('App\Mating', 'cat_id_2');
    }
}
