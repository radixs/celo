<?php namespace Library\Models;

class ConsoleInputHistory extends AbstractTable
{
    const TABLE       = 'console_input_history';
    const ID_COLUMN   = 'id';
    const TYPE_COLUMN = 'type';
    const TEXT_COLUMN = 'text';
    const SENT_COLUMN = 'sent';

    const DEFAULT_ROW_LIMIT = 100;

    const TYPE_SERVER = 'server';
    const TYPE_NODE   = 'node';

    //todo description
    public function __construct(\PDO $db)
    {
        parent::__construct($db, self::TABLE);
    }

    //todo description
    public function get_initial_history_set()
    {
        $results_server = $this->get_rows(
            [self::TEXT_COLUMN],
            [self::TYPE_COLUMN => self::TYPE_SERVER],
            [self::ID_COLUMN],
            AbstractTable::ORDER_ASCENDING,
            self::DEFAULT_ROW_LIMIT
        );

        $results_node = $this->get_rows(
            [self::TEXT_COLUMN],
            [self::TYPE_COLUMN => self::TYPE_NODE],
            [self::ID_COLUMN],
            AbstractTable::ORDER_ASCENDING,
            self::DEFAULT_ROW_LIMIT
        );

        $console_history_set = [
            self::TYPE_SERVER => $results_server,
            self::TYPE_NODE   => $results_node
        ];

        return $console_history_set;
    }
}
