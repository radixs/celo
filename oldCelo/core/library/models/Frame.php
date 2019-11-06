<?php namespace Library\Models;

class Frame
{

    const DIVIDER_START = "-----------------------";
    const DIVIDER_END   = "-----------------------\n";
    const TABLE_NAME    = 'frame_list';

    const STATE_RUNNING   = 1;
    const STATE_COMPLETED = 2;

    private $id;

    protected $number;
    protected $start_time;
    protected $end_time;
    protected $time_taken;
    protected $start_memory;
    protected $memory_used;
    protected $session_peak;


    /**
     * Frame constructor.
     *
     * @param $number int  Current frame number assigned by the controller.
     * @param \PDO $db
     * @param $session_start string date("Y-m-d H:i:s")
     */
    public function __construct($number, \PDO $db, $session_start)
    {
        $this->start_memory = memory_get_usage();
        $this->number       = $number;
        $this->start_time   = date("Y-m-d H:i:s");

        //insert a new row into frame list table with initial state
        try {
            $state   = self::STATE_RUNNING;
            $columns = 'state, number, session_started, created';

            $db->beginTransaction();
            $stmt = $db->prepare('INSERT INTO '.self::TABLE_NAME.
                ' ('.$columns.') VALUES (?, ?, ?, ?)');
            $stmt->bindParam(1, $state, \PDO::PARAM_INT);
            $stmt->bindParam(2, $this->number, \PDO::PARAM_INT);
            $stmt->bindParam(3, $session_start, \PDO::PARAM_STR);
            $stmt->bindParam(4, $this->start_time, \PDO::PARAM_STR);
            $stmt->execute();
            $this->id = $db->lastInsertId();
            $db->commit();

        } catch (\PDOException $e) {
            $db->rollBack();
            err_log('Unable to add new frame, database error. '.$e->getMessage()."\n");
            exit();
        }
    }


    /**
     * Process the frame. Perform Salvilines step.
     * @param \PDO $db
     */
    public function process(\PDO $db)
    {



        //TODO process patterns and inputs
        //refer to the gui/todo file




        $this->save_statistics($db);
    }


    /**
     * Store processing report data in the database.
     *
     * @param \PDO $db
     */
    protected function save_statistics(\PDO $db)
    {
        $this->end_time = date("Y-m-d H:i:s");

        $start_time         = strtotime($this->start_time);
        $end_time           = strtotime($this->end_time);
        $this->time_taken   = $end_time - $start_time;
        $this->memory_used  = memory_get_usage() - $this->start_memory;
        $this->session_peak = memory_get_peak_usage();

        try {
            $state = self::STATE_COMPLETED;

            $db->beginTransaction();
            $stmt = $db->prepare('UPDATE '.self::TABLE_NAME.
                ' SET state = ?,
                 ended = ?,
                 time_taken = ?,
                 memory_used = ?,
                 session_peak_memory = ?
                 WHERE id = '.$this->id);

            $stmt->bindParam(1, $state, \PDO::PARAM_INT);
            $stmt->bindParam(2, $this->end_time, \PDO::PARAM_STR);
            $stmt->bindParam(3, $this->time_taken, \PDO::PARAM_INT);
            $stmt->bindParam(4, $this->memory_used, \PDO::PARAM_INT);
            $stmt->bindParam(5, $this->session_peak, \PDO::PARAM_INT);
            $stmt->execute();
            $db->commit();

        } catch (\PDOException $e) {
            $db->rollBack();
            err_log('Unable to save frame statistics. '.$e->getMessage()."\n");
            exit();
        }
    }


    /**
     * Return frame processing statistics.
     */
    public function get_processing_report()
    {
        return
            "Frame took $this->time_taken seconds to complete, peak memory usage was "
            .$this->session_peak." bytes. Frame used "
            .$this->memory_used." bytes of memory.\n";
    }


    /**
     * Return current frame number.
     *
     * @return int
     */
    public function get_number()
    {
        return $this->number;
    }


    /**
     * Return frame opening header.
     *
     * @return string
     */
    public function get_header()
    {
        return self::DIVIDER_START.' Frame '.$this->number.' '.self::DIVIDER_END;
    }


    /**
     * Remove all rows from frame list table.
     *
     * @param \PDO $db
     */
    public static function delete_all_rows(\PDO $db)
    {
        try {
            $db->beginTransaction();
            $db->exec('TRUNCATE '.self::TABLE_NAME);
            $db->commit();
        } catch (\PDOException $e) {
            $db->rollBack();
            err_log('Unable to clear frame data from the database. '.
                $e->getMessage()."\n");
            exit();
        }
    }
}
