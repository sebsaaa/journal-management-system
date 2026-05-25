<?php namespace Seba\Punktoza\Models;

use Model;

/**
 * Journal Model
 *
 * @link https://docs.octobercms.com/4.x/extend/system/models.html
 */
class Journal extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string table name
     */
    public $table = 'seba_punktoza_journals';

    protected $fillable = ['uid','title','issn','eissn','points','disciplines'];

    protected $casts = ['points'=>'integer','disciplines'=>'json'];

    /**
     * @var array rules for validation
     */
    public $rules = [];
}
