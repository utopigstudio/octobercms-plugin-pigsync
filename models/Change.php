<?php
namespace Utopigs\Pigsync\Models;

use Model;

/**
 * Model
 */
class Change extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'utopigs_pigsync_changes';
}
