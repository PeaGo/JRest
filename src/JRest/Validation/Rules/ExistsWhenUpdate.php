<?php

namespace JRest\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class ExistsWhenUpdate extends AbstractRule
{

    /**
     * @var integer The id of the user
     */
    protected $id;

    /**
     * @var
     */
    private $columns;

    /**
     * @var \Illuminate\Database\Query\Builder
     */
    private $table;

    public function __construct($table, $columns, $id)
    {
        $this->table = $table;
        $this->columns = $columns;
        $this->id = $id;
    }

    public function validate($input): bool
    {
        return !$this->table->where($this->columns, $input)
            ->where('id', '!=', $this->id)->exists();
    }
}
