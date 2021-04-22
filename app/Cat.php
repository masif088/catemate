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
 * @property int $vaccine
 * @property string $last_parasite
 * @property string $last_vaccine
 * @property int $sex
 * @property string $photo
 * @property string $created_at
 * @property string $updated_at
 * @property Race $race
 * @property User $user
 * @property Mating[] $matings1
 * @property Mating[] $matings2
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
    protected $fillable = ['user_id', 'race_id', 'name', 'status', 'birth', 'vaccine', 'last_parasite', 'last_vaccine', 'sex', 'photo', 'created_at', 'updated_at'];

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
    public function matings1()
    {
        return $this->hasMany('App\Mating', 'cat_id_1');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function matings2()
    {
        return $this->hasMany('App\Mating', 'cat_id_2');
    }
}
