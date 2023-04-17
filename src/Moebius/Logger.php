<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */

namespace App\Moebius;

/**
 * Class Logger
 * @package App\Moebius
 * @thanks to Luca Tortora <misfotto@gmail.com>
 */
final class Logger {

    var ?string $uniqid = null;
    var bool $func_trace = false;
    var $greylog_url;
    var bool $grey_log_on = false;

    /**
     * @param string $filename
     * @param false $with_uniq_id
     * @param false $called_function
     */
    function __construct(string $filename = 'general.log', bool $with_uniq_id = false, bool $called_function = false, $gray_log = false)
    {
        $this->greylog_url = getenv('LOG_SERVER');
        $this->filename = __DIR__ . '/../../logs/' . $filename;

        /*
         * Setting the unique id to track logger single instance
         *
         */
        if ($with_uniq_id) {
            $this->uniqid = "[ " . uniqid() . " ]";
        }

        if ($called_function) {
            $this->func_trace = true;
        }

        return $this;
    }

    /**
     * @param null $message The message
     * @param string $type The type of message
     * @return false|string|null
     */
    function log_action($message = null, string $type = 'INFO')
    {
        try {
            $logfile_descriptor = new \SplFileObject($this->filename, "a+");
            $logfile_descriptor->fwrite($this->_format_log_message($message, $type));
            return $this->uniqid;

        } catch (\Exception $e) {
            echo "Something went terribly wrong... \n" . $e->getMessage() . "\n-----\n";
            return false;
        }
    }

    /*
     * In a very "rustic" way formats the log entry with basic infos.
     */
    private function _format_log_message($message = null, $type = 'INFO'): string
    {


        $date = date("Y-M-d H:i:s.u");
        /*
         * If the logger is constructed with uniqid support
         * adds it to the log message, otherwise adds an empty string
         */
        $uniqid = ($this->uniqid != null) ? $this->uniqid : "";
        $called_function = ($this->func_trace) ? "[ " . @debug_backtrace()[2]['function'] . " ]" : "";

        if ( is_array($message) ) {
            $message = json_encode($message);
        }

        return "[ {$date} ]{$uniqid}{$called_function}:[{$type}] {$message}\n";

    }

    function info($message)
    {
        return $this->log_action($message, 'INFO');
    }

    function warning($message)
    {
        return $this->log_action($message, 'WARNING');
    }

    function error($message)
    {
        return $this->log_action($message, 'ERROR');
    }

    function critical($message)
    {
        return $this->log_action($message, 'CRITICAL');
    }
}
