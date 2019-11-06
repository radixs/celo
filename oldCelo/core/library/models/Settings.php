<?php namespace Library\Models;

class Settings extends AbstractValuesTable
{
    const TABLE         = 'settings';
    const NAME_COLUMN   = 'parameter_name';
    const VALUES_COLUMN = 'parameter_value';

    public function __construct(\PDO $db)
    {
        parent::__construct($db, self::TABLE, self::NAME_COLUMN, self::VALUES_COLUMN);
    }
}
