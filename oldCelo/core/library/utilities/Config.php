<?php namespace Library\Utilities;

class Config
{
    private $temp_config = '/celo/core/config.ini'; //TODO remove once locate_config_ini is properly done
    private $configuration = [];

    /**
     * Config constructor.
     */
    public function __construct()
    {
        $path = $this->locate_config_ini();
        if ($path !== false) {
            $this->configuration = $this->parse_config($path);
        } else {
            err_log("Unable to locate config file!\n");
            exit();
        }
    }

    /**
     * Extract config value.
     *
     * @param $name
     * @return null
     */
    public function __get(string $name)
    {
        foreach ($this->configuration as $section => $settings) {
            if (!empty($settings[$name])) {
                return $settings[$name];
            }
        }
        err_log("Config option '".$name."' not found in the config file!\n'");
        return null;
    }


    /**
     * Parse the config file under provided path.
     *
     * @param $path
     * @return array
     */
    protected function parse_config(string $path)
    {
        return parse_ini_file($path, true);
    }


    /**
     * Traverse dir structure down to locate config.ini file.
     *
     * TODO add proper searching
     * TODO https://packagist.org/packages/symfony/finder
     * TODO https://symfony.com/doc/current/components/finder.html
     *
     * @return bool|string
     */
    protected function locate_config_ini()
    {
        if (file_exists($this->temp_config)) {
            return $this->temp_config;
        }
        return false;
    }
}
