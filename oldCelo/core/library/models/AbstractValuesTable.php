<?php namespace Library\Models;

abstract class AbstractValuesTable extends AbstractTable
{
    const EMPTY_VALUE = 'empty';

    protected $name_column;
    protected $values_column;


    /**
     * AbstractValuesTable constructor.
     *
     * Register all required parameters to navigate the given table.
     *
     * @param \PDO $db
     * @param string $table
     * @param string $name_column
     * @param string $values_column
     */
    public function __construct(
        \PDO $db,
        string $table,
        string $name_column,
        string $values_column
    )
    {
        parent::__construct($db, $table);
        $this->name_column   = $name_column;
        $this->values_column = $values_column;
    }


    /**
     * Get values set from the table.
     *
     * @return array
     */
    public function get_values(): array
    {
        $extracted_values_set = [];
        try {
            $results = $this->db->query('SELECT * FROM '.$this->table);
            foreach ($results as $row) {
                if (!empty($row[$this->name_column])
                    && isset($row[$this->values_column])) {
                    $extracted_values_set[$row[$this->name_column]]
                        = $row[$this->values_column];
                }
            }
        } catch (\PDOException $e) {
            err_log($e->getMessage());
            exit();
        }
        return $extracted_values_set;
    }


    /**
     * Compare current values stored in the database to
     * the input and return an array containing only those
     * that are different (from the database).
     *
     * @param array $old_values  Old values to compare against.
     * @param bool $replace_null_with_empty  Perform an extra conversion.
     * @return array  Values form the database.
     */
    public function get_changed_values(
        array $old_values,
        $replace_null_with_empty = false
    ): array
    {
        $current_values = $this->get_values();
        err_log('current values'); //TODO remove
        err_log($current_values); //TODO remove

        //replace null values with 'empty' string so GUI can empty a stat
        if ($replace_null_with_empty === true) {
            foreach ($current_values as $name => &$value) {
                if (is_null($value)) {
                    $value = self::EMPTY_VALUE;
                }
            }
        }

        //compare new data to old and return only changed positions
        $new_values = [];
        foreach ($old_values as $column_name => $column_value) {
            if (isset($current_values[$column_name])
                && (string)$column_value !== (string)$current_values[$column_name]) {
                $new_values[$column_name]
                    = (string)$current_values[$column_name];
            }
        }
        return $new_values;
    }


    /**
     * Update table value, string only.
     *
     * @param string $column_name
     * @param string $value
     *
     * @return string|bool
     */
    public function update_text_value(string $column_name, string $value)
    {
        try {
            $this->db->query('UPDATE '.$this->table.' SET '.$this->values_column.'="'.$value.'" WHERE '.$this->name_column.'="'.$column_name.'"');
        } catch (\PDOException $e) {
            return 'Error from database: '.$e->getMessage();
        }
        return true;
    }


    /**
     * Extract single value form the table and format with table name as 1st level array.
     *
     * @param string $column_name
     *
     * @return string|array
     */
    public function get_formatted_value(string $column_name)
    {
        $extracted_values_set = [];
        try {
            $results = $this->db->query('SELECT '.$this->values_column.' FROM '.$this->table.' WHERE '.$this->name_column.'="'.$column_name.'"');
            foreach ($results as $row) {
                $extracted_values_set[$this->table][$column_name] = $row[$this->values_column];
            }
        } catch (\PDOException $e) {
            return 'Database error while trying to get a value: '.$e->getMessage();
        }
        return $extracted_values_set;
    }
}
