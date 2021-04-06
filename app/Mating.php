<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $cat_id_1
 * @property integer $cat_id_2
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property Cat $cat
 * @property Cat $cat
 */
class Mating extends Model
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
    protected $fillable = ['cat_id_1', 'cat_id_2', 'status', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cat_1()
    {
        return $this->belongsTo('App\Cat', 'cat_id_1');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cat_2()
    {
        return $this->belongsTo('App\Cat', 'cat_id_2');
    }
}
