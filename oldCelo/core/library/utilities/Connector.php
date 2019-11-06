<?php namespace Library\Utilities;

class Connector
{
    protected $credentials = [
        'server'   => null,
        'database' => null,
        'user'     => null,
        'password' => null
    ];

    protected $error = false;


    /**
     * Connector constructor.
     *
     * Loads connection parameters from ini file.
     *
     * @param string $path Path to config file.
     * @param string $mode
     */
    public function __construct(string $path, string $mode)
    {
        $this->load_from_ini($path, $mode);
    }


    /**
     * Establish connection and return the handler.
     *
     * @return \PDO
     */
    public function connect(): \PDO
    {
        if ($this->error === false) {
            $server   = $this->credentials['server'];
            $database = $this->credentials['database'];
            $user     = $this->credentials['user'];
            $password = $this->credentials['password'];

            try {
                $db = new \PDO(
                    'mysql:host='.$server.';dbname='.$database.';charset=utf8',
                    $user,
                    $password
                );
                //this enables exceptions being thrown by the PDO
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                err_log($e->getMessage()."\n");
                exit();
            }
            return $db;

        } else {
            exit(
            "Unable to connect, connection configuration error, shutting down...\n"
            );
        }
    }


    /**
     * Format input value so that it can be used in a query.
     *
     * @param $value
     * @return int|string
     */
    public static function encode_value_for_query($value)
    {
            if (is_string($value)) { //adds string quotes
                return "'".$value."'";
            } elseif (is_bool($value)) {
                if ($value === true) { //converts to tinyint
                    return 1;
                } else {
                    return 0;
                }
            } else { //leave int and float as is
                return $value;
            }
    }


    /**
     * Search in the standard location for the ini file and then parse the config.
     *
     * @param string $path  Path to config file.
     * @param string $section_name
     */
    protected function load_from_ini(string $path, string $section_name)
    {
        if (file_exists($path)) {
            $config = parse_ini_file($path, true);

            if (!empty($config[$section_name])) {
                foreach ($this->credentials as $parameter_name => $parameter_value) {
                    if (!empty($config[$section_name][$parameter_name])) {
                        $this->credentials[$parameter_name] =
                            $config[$section_name][$parameter_name];
                    } else {
                        $this->error = "Cannot find \"".$parameter_name."\" parameter!\n";
                        err_log($this->error);
                        break;
                    }
                }

            } else {
                $this->error = "Connection credentials for ["
                    .$section_name."] not found!\n";
                err_log($this->error);
            }

        } else {
            $this->error = "Unable to find config file in ".$path."!";
            err_log($this->error);
        }
    }
}
