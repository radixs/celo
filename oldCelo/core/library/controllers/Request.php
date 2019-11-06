<?php namespace Library\Controllers;

use Library\Models\{
    ConsoleInputHistory, StatisticsAverages, Settings
};

class Request
{
    const ERROR = 'Unable to process request. Action not recognized.';

    protected $db;


    /**
     * Set up dependencies.
     * .
     * @param \PDO $db
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }


    /**
     * Main request routing method.
     *
     * @param array $data Array containing incoming information.
     */
    public function process_request(array $data)
    {

        if (!empty($data['action'])) {
            $action = $data['action'];

            switch ($action) {
                case 'send_loop_start':
                    //TODO add some bit that checks if this is not running already.
                    //TODO Prevent multiple core loop initiations.
                    exec('php ../core/frame.php > /var/log/php_errors.log 2>&1 &');
                    $this->send_response('Loop started.');
                    break;
                case 'send_loop_end':
                    FrameController::set_shutdown_flag($this->db, true);
                    $this->send_response('Loop ended.');
                    break;
                case 'fetch_state': //use only to synchronise all settings and statistics with gui
                    $new_state = $this->form_state_response($data);
                    $this->send_response($new_state);
                    break;
                case 'fetch_console_history':
                    $console_input_history_model = new ConsoleInputHistory($this->db);
                    $this->send_response(
                        [ConsoleInputHistory::TABLE => $console_input_history_model->get_initial_history_set()]
                    );
                    break;
                case 'register_command':
                    $console_input_history_model = new ConsoleInputHistory($this->db);

                    $target_columns = [ConsoleInputHistory::TYPE_COLUMN, ConsoleInputHistory::TEXT_COLUMN];
                    $input_values   = [$data['type'], $data['input']];
                    $insert_result  = $console_input_history_model->insert_one_row(
                        $target_columns,
                        $input_values
                    );
                    $this->send_response('Command entering result: '.(string)$insert_result);
                    break;
                //update separate server setting options
                case 'interval_microseconds':
                case 'forget_previous_session':
                case 'shutdown_flag':
                case 'report_last_x_frames':
                case 'hold_last_x_frames':
                case 'polling_frequency':
                    if (isset($data['value'])) {
                        $value          = $data['value'];
                        $settings_model = new Settings($this->db);
                        $update_result  = $settings_model->update_text_value($action, $value);
                        if ($update_result === true) {
                            $this->send_response($settings_model->get_formatted_value($action));
                        } else {
                            $this->send_response('Update result: '.$update_result);
                        }
                    } else {
                        $this->send_response('No value to update setting.');
                    }
                    break;
                default:
                    $this->send_response(self::ERROR);
            }
        }
    }


    /**
     * Echo a response back to the client.
     *
     * @param mixed $data  Data containing response to be sent back.
     */
    public function send_response($data)
    {
        if (is_array($data)) {
            echo json_encode($data);
        } else {
            echo $data;
        }
    }


    /**
     * Check if any settings or statistics have changed and return those changes.
     *
     * @param $data
     * @return array
     */
    protected function form_state_response($data)
    {
        $new_data = [];

        err_log('incoming request data'); //TODO remove
        err_log($data); //TODO remove
        $settings   = $data['settings'];
        $statistics = $data['statistics'];

        $settings_model = new Settings($this->db);
        $new_settings   = $settings_model->get_changed_values($settings);
        err_log('new_settings'); //TODO remove
        err_log($new_settings); //TODO remove
        if (!empty($new_settings)) {
            $new_data['settings'] = $new_settings;
        }

        $statistics_model = new StatisticsAverages($this->db);
        $new_statistics   = $statistics_model->get_changed_values($statistics, true);
        if (!empty($new_statistics)) {
            $new_data['statistics'] = $new_statistics;
        }

        err_log('returned data'); //TODO remove
        err_log($new_data); //TODO remove
        return $new_data;
    }


}
