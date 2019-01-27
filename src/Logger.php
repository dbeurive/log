<?php

/**
 * This file implements the logger.
 *
 * The organisation of one line of LOG is:
 *
 * Date SessionId Level LinearizationFlag Message
 *
 * With:
 *
 * - Date: YYYYMMDDHHMMSS.
 * - SessionId: a unique ID.
 * - Level: FATAL, ERROR, WARNING, INFO, DATA or DEBUG.
 * - LinearizationFlag: L (linearized) or R (raw).
 */

namespace dbeurive\Log;

/**
 * Class Logger
 *
 * This class implements the logger.
 *
 * @package dbeurive\Squirrel
 */

class Logger
{
    const LEVEL_FATAL   = 0;
    const LEVEL_ERROR   = 1;
    const LEVEL_WARNING = 2;
    const LEVEL_SUCCESS = 3;
    const LEVEL_INFO    = 4;
    const LEVEL_DATA    = 5;
    const LEVEL_DEBUG   = 6;

    /** @var string Path to the LOG file. */
    private $__logFilePath;
    /** @var int The LOG level. */
    private $__level;
    /** @var array List of LOG levels. */
    private $__levels;
    /** @var string the session's ID. */
    private $__sessionId;
    /** @var string The sequence of characters that represents a new line. */
    private $__newLine = "\n";

    /**
     * Logger constructor.
     * @param string $in_path Path to the LOG file.
     * @param int $in_level Required LOG level. Values can be:
     *        - Logger::LEVEL_FATAL
     *        - Logger::LEVEL_ERROR
     *        - Logger::LEVEL_WARNING
     *        - Logger::LEVEL_SUCCESS
     *        - Logger::LEVEL_INFO
     *        - Logger::LEVEL_DATA
     *        - Logger::LEVEL_DEBUG
     * @param null|string $in_opt_session_id The session ID.
     * @see Logger::LEVEL_FATAL
     * @see Logger::LEVEL_ERROR
     * @see Logger::LEVEL_WARNING
     * @see Logger::LEVEL_SUCCESS
     * @see Logger::LEVEL_INFO
     * @see Logger::LEVEL_DATA
     * @see Logger::LEVEL_DEBUG
     * @throws \Exception
     */
    public function __construct($in_path, $in_level, $in_opt_session_id=null) {
        $this->__logFilePath = $in_path;
        $this->__level = $in_level;
        $this->__levels = self::__getLevels();
        $this->__sessionId = is_null($in_opt_session_id) ? $this->__getSessionId() : $in_opt_session_id;
    }

    /**
     * Set the sequence of characters used as the new line delimiter.
     * @param string $in_delimiter The new line delimiter.
     */
    public function setNewLine($in_delimiter) {
        $this->__newLine = $in_delimiter;
    }

    /**
     * Create a session ID.
     * @return string Return the session ID.
     */
    private function __getSessionId() {
        return uniqid();
    }

    /**
     * Log a message into the LOG file.
     * @param int $in_level LOG level.
     * @param string $in_message Message to LOG.
     * @throws \Exception
     */
    private function __log($in_level, $in_message) {
        if ($in_level > $this->__level) {
            return;
        }
        $in_level = $this->__getLevelTag($in_level);
        $ss = $this->__sessionId;
        $tt = strftime('%Y%m%d%-H%M%S');
        $linearized = false;

        if (self::needLinearization($in_message)) {
            $in_message = self::linearize($in_message);
            $linearized = true;
        }

        if (false === file_put_contents($this->__logFilePath, sprintf("%s %s %s %s %s %s",
                $tt,
                $ss,
                $in_level,
                $linearized ? 'L' : 'R',
                $in_message,
                $this->__newLine), FILE_APPEND)) {
            throw new \Exception('Can not write into my LOG file "' . $this->__logFilePath . '": ' . error_get_last()['message']);
        }
    }

    /**
     * Log a fatal error.
     * @param string $in_message Message to log.
     * @return $this
     * @throws \Exception
     */
    public function fatal($in_message) {
        $this->__log(self::LEVEL_FATAL, $in_message);
        return $this;
    }

    /**
     * Log an error message.
     * @param string $in_message Message to log.
     * @return $this
     * @throws \Exception
     */
    public function error($in_message) {
        $this->__log(self::LEVEL_ERROR, $in_message);
        return $this;
    }

    /**
     * Log a warning message.
     * @param string $in_message Message to log.
     * @return $this
     * @throws \Exception
     */
    public function warning($in_message) {
        $this->__log(self::LEVEL_WARNING, $in_message);
        return $this;
    }

    /**
     * Log a success message.
     * @param string $in_message Message to log.
     * @return $this
     * @throws \Exception
     */
    public function succes($in_message) {
        $this->__log(self::LEVEL_SUCCESS, $in_message);
        return $this;
    }

    /**
     * Log an informative message.
     * @param string $in_message Message to log.
     * @return $this
     * @throws \Exception
     */
    public function info($in_message) {
        $this->__log(self::LEVEL_INFO, $in_message);
        return $this;
    }

    /**
     * Log a message (string, numerical value or boolean) or a data (array, object or resource).
     * @param mixed $in_message Message or data.
     * @return $this
     * @throws \Exception
     */
    public function data($in_message) {
        if (is_scalar($in_message)) {
            if (is_bool($in_message)) {
                $in_message = $in_message ? 'true' : 'false';
            }
            $this->__log(self::LEVEL_DATA, $in_message);
        } else {
            $this->__log(self::LEVEL_DATA, print_r($in_message, true));
        }
        return $this;
    }

    /**
     * Log a debug message.
     * @param string $in_message Message to log.
     * @return $this
     * @throws \Exception
     */
    public function debug($in_message) {
        $this->__log(self::LEVEL_DEBUG, $in_message);
        return $this;
    }

    /**
     * Test whether a text needs to be linearised or not.
     * @param string $in_text Text to test.
     * @return bool If the text needs to be linearised, then the method returns the value true.
     *         Otherwise, it returns the value false.
     */
    static public function needLinearization($in_text) {
        return 1 === preg_match('/[\r\n]/', $in_text);
    }

    /**
     * Linearize a given text.
     * @param string $in_text Text to linearize.
     * @return string The method returns the linearized text.
     */
    static public function linearize($in_text) {
        return rawurlencode($in_text);
    }

    /**
     * Delinearize a given text.
     * @param string $in_text Text to "delinearize".
     * @return string The method returns the "delinearized" text.
     */
    static public function delinearize($in_text) {
        return rawurldecode($in_text);
    }

    /**
     * Return the associations between a LOG level, defined as integer values, and their names.
     * @return array The method returns an associative array which:
     *         - Keys are integers' values.
     *         - Values are levels' names.
     * @throws \Exception
     */
    static private function __getLevels() {
        $oClass = new \ReflectionClass(__CLASS__);
        $res = array();
        foreach ($oClass->getConstants() as $_name => $_value) {
            $matches = array();
            if (preg_match('/^LEVEL_(.+)$/', $_name, $matches)) {
                $res[$_value] = $matches[1];
            }
        }
        return $res;
    }

    /**
     * Return the level's name associated to a given integer's value.
     * @param int $in_value Integer's value that represents the LOG level.
     * @return string The function returns the name of the LOG's level.
     */
    private function __getLevelTag($in_value) {
        if (! array_key_exists($in_value, $this->__levels)) {
            return 'XXXX';
        }
        return $this->__levels[$in_value];
    }
}