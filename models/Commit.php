<?php namespace Utopigs\Pigsync\Models;

use Model;

/**
 * Model
 */
class Commit extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $hasMany = [
        'changes' => Change::class
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'utopigs_pigsync_commits';
}
