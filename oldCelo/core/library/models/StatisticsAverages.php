<?php namespace Library\Models;

class StatisticsAverages extends AbstractValuesTable
{
    const TABLE         = 'statistics_averages';
    const NAME_COLUMN   = 'statistics_name';
    const VALUES_COLUMN = 'statistics_value';


    /**
     * Register database handler.
     *
     * @param \PDO $db
     */
    public function __construct(\PDO $db)
    {
        parent::__construct($db, self::TABLE, self::NAME_COLUMN, self::VALUES_COLUMN);
    }
}
