<?php namespace Library\Models;

abstract class AbstractTable
{
    const ORDER_DESCENDING = 'DESC';
    const ORDER_ASCENDING  = 'ASC';

    protected $db; //database handler
    protected $table; //table name

    /**
     * AbstractTable constructor.
     * @param \PDO $db
     * @param string $table_name
     */
    public function __construct(\PDO $db, string $table_name)
    {
        $this->db    = $db;
        $this->table = $table_name;
    }


    /**
     * Generic getter.
     *
     * Supports different kinds of filtering.
     *
     * @param array $columns           Optional. List of columns to fetch the values.
     * @param array|null $where        Optional. Array of conditions to filter by.
     * @param array $order_by          Optional. Column to use for the ordering of the results.
     * @param string $order_direction  Optional. The direction of the ordering.
     * @param int|null $limit          Optional. Limits the number of returned rows.
     * @return array  Returned data set.
     */
    public function get_rows(
        array $columns = ['*'],
        array $where = null,
        array $order_by = ['id'],
        string $order_direction = self::ORDER_DESCENDING,
        int $limit = null
    )
    {
        $query = 'SELECT '.$this->column_array_to_string($columns).' FROM '.$this->table;
        if (!empty($where)) {
            $query .= $this->format_where_clause($where);
        }
        $query .= ' ORDER BY '.$this->column_array_to_string($order_by).' '.$order_direction;
        if (!empty($limit)) {
            $query .= ' LIMIT '.$limit;
        }
        $results = $this->db->query($query, \PDO::FETCH_ASSOC);
        return $results->fetchAll();
    }


    /**
     * Insert one row into the table.
     *
     * @param array $columns  Columns to insert the values into.
     * @param array $values   Values to be inserted.
     * @param bool $replace   Optional. Specify if existing records should be updated.
     * @return bool  Operation result.
     */
    public function insert_one_row(array $columns, array $values, bool $replace = false)
    {
        $number_of_columns = count($columns);
        $number_of_values  = count($values);

        $mode = 'INSERT';
        if ($replace === true) {
            $mode = 'REPLACE';
        }

        if ($number_of_columns === $number_of_values) {
            $column_list = [];
            $values_list = [];
            for ($column_value_index = 0; $column_value_index < $number_of_columns; $column_value_index++) {
                $column_list[] = $columns[$column_value_index];
                $values_list[] = '"'.$values[$column_value_index].'"';
            }
            $query =
                $mode
                .' INTO '
                .$this->table
                .' ('
                .$this->column_array_to_string($column_list)
                .') VALUES ('
                .$this->column_array_to_string($values_list)
                .')';
            $this->db->query($query);
            return true;
        }
        return false;
    }


    /**
     * Arrange array items into a string.
     *
     * @param array $columns
     * @return string
     */
    protected function column_array_to_string(array $columns)
    {
        return implode(',', $columns);
    }


    /**
     * Build a WHERE clause part of the query string from the supplied array of conditions.
     *
     * @param array $conditions  Must be arranged as column name => value
     * @return string  Formatted WHERE clause string
     */
    protected function format_where_clause(array $conditions)
    {
        $compressed_string = ' WHERE ';
        $formatted_clauses = [];
        foreach ($conditions as $column_name => $column_value) {
            $formatted_clauses[] = "`".$column_name."` = '".$column_value."'";
        }
        $compressed_string .= implode(' AND ', $formatted_clauses);

        return $compressed_string;
    }
}