<?php namespace Library\Controllers;

use Library\Models\{Frame, Settings};
use Library\Utilities\{Config};

class FrameController
{

    const SETTINGS_TABLE = 'settings';
    const COLUMN_NAME    = 'parameter_name';
    const COLUMN_VALUE   = 'parameter_value';

    /** @var Config $config  Configuration from config.ini. */
    protected $config;
    /** @var array $settings Settings from the database. */
    protected $settings = [];

    protected $interval      = 0;
    protected $current_frame = 1;
    public $session_start;


    /**
     * Frame constructor.
     *
     * @param Config $config
     * @param \PDO $db
     */
    public function __construct(Config $config, \PDO $db)
    {
        echo "Frame looper started!\n";
        echo "Fetching settings...\n";
        self::set_shutdown_flag($db, false); //reset the shutdown flag just in case
        $this->refresh_settings($db);
        $this->config = $config;
        echo "Settings loaded.\n";
    }


    /**
     * Main control flow.
     *
     * Launch frames until app is terminated.
     *
     * @param \PDO $db
     */
    public function run(\PDO $db)
    {
        $this->initialise($db);

        while (true) {
            $frame = new Frame($this->current_frame, $db, $this->session_start);
            $frame->process($db);

            echo $frame->get_header();
            echo $frame->get_processing_report();
            $this->current_frame++;

            $this->refresh_settings($db);
            $this->update_statistics($db);

            if (!empty($this->settings['shutdown_flag'])
                && $this->settings['shutdown_flag'] === '1') {
                $this->shutdown($db);
            }

            unset($frame);
            if ($this->interval > 0) {
                usleep($this->interval);
            }
        }
    }


    /**
     * Run all necessary actions before frame loop starts.
     * This is mostly configuration stuff and clean-up
     * calls.
     *
     * @param \PDO $db
     */
    protected function initialise(\PDO $db)
    {
        if (!empty($this->settings['interval_microseconds'])
            && $this->settings['interval_microseconds'] > 0) {
            $this->interval = $this->settings['interval_microseconds'];
        }

        if (!empty($this->settings['forget_previous_session'])
            && $this->settings['forget_previous_session'] === '1') {
            $this->forget_previous_session($db);
        }

        $this->session_start = date("Y-m-d H:i:s");
    }


    /**
     * TODO clean up stats (add this frame data to stats and produce new averages)
     *
     * @param \PDO $db
     */
    protected function update_statistics(\PDO $db)
    {
        //TODO !! add max frame history setting, add how many frames to use to generate statistics, add statistics refresh frequency
        //TODO create frame average table
    }


    /**
     * Remove any data and logs from frame related tables.
     *
     * @param \PDO $db
     */
    protected function forget_previous_session(\PDO $db)
    {
        Frame::delete_all_rows($db);
    }


    /**
     * Reload all settings from the database.
     *
     * @param \PDO $db
     */
    public function refresh_settings(\PDO $db)
    {
        $settings_model = new Settings($db);
        $this->settings = $settings_model->get_values();
    }


    /**
     * Stop all processes and exit application.
     *
     * @param \PDO $db
     */
    protected function shutdown(\PDO $db)
    {
        echo "Shutdown flag detected, attempting a graceful shutdown!\n";
        self::set_shutdown_flag($db, false); //reset the flag back
        echo "Graceful shutdown successful!\n";
        echo "Bye bye!\n";
        exit();
    }


    /**
     * Updates database shutdown flag.
     *
     * @param \PDO $db
     * @param bool $mode
     */
    public static function set_shutdown_flag(\PDO $db, bool $mode)
    {
        $mode = (int)$mode;

        try {
            $db->beginTransaction();
            $stmt = $db->prepare('UPDATE '.self::SETTINGS_TABLE.
                ' SET parameter_value = ? WHERE parameter_name = "shutdown_flag"');
            $stmt->bindParam(1, $mode, \PDO::PARAM_INT);
            $stmt->execute();
            $db->commit();

        } catch (\PDOException $e) {
            err_log('Unable to write to database while setting shutdown flag to '
                .$mode.'. '.$e->getMessage());
        }
    }
}
