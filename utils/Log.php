<?php
/** 简单的log类 */

interface ILogHandler
{
    public function write($msg);

}

class CLogFileHandler implements ILogHandler
{
    private $handle = null;

    public function __construct($file = '')
    {
        $this->handle = fopen($file, 'a');
    }

    public function write($msg)
    {
        fwrite($this->handle, $msg, 4096);
    }

    public function __destruct()
    {
        fclose($this->handle);
    }
}

//设置默认日志等级
if (!defined('LOG_LEVEL')) {
    define('LOG_LEVEL', 15);
}

class Log
{
    private $handler = null;
    private $level   = LOG_LEVEL;

    private static $instance = null;

    private function __construct() { }

    private function __clone() { }

    public static function Init($handler = null, $level = LOG_LEVEL)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
            self::$instance->__setHandle($handler);
            self::$instance->__setLevel($level);
        }
        return self::$instance;
    }


    private function __setHandle($handler)
    {
        $handler || $handler = $logHandler = new CLogFileHandler("logs/" . date('Y-m-d') . '.log');
        $this->handler = $handler;
    }

    private function __setLevel($level)
    {
        $this->level = $level;
    }

    public static function DEBUG($msg)
    {
        self::write(1, $msg);
    }

    public static function WARN($msg)
    {
        self::write(4, $msg);
    }

    public static function ERROR($msg)
    {
        $debugInfo = debug_backtrace();
        $stack     = "[";
        foreach ($debugInfo as $key => $val) {
            if (array_key_exists("file", $val)) {
                $stack .= ",file:" . $val["file"];
            }
            if (array_key_exists("line", $val)) {
                $stack .= ",line:" . $val["line"];
            }
            if (array_key_exists("function", $val)) {
                $stack .= ",function:" . $val["function"];
            }
        }
        $stack .= "]";
        self::write(8, $stack . $msg);
    }

    public static function INFO($msg)
    {
        self::write(2, $msg);
    }

    private function getLevelStr($level)
    {
        switch ($level) {
            case 1:
                return 'debug';
                break;
            case 2:
                return 'info';
                break;
            case 4:
                return 'warn';
                break;
            case 8:
                return 'error';
                break;
            default:

        }
    }

    protected function _write($level, $msg)
    {
        if (($level & $this->level) == $level) {
            $msg = '[' . date('Y-m-d H:i:s') . '][' . $this->getLevelStr($level) . '] ' . $msg . "\n";
            $this->handler->write($msg);
        }
    }

    public static function write($level, $msg)
    {
        if (!self::$instance) {
            self::$instance = self::Init();
        }
        is_array($msg) && $msg = json_encode($msg);
        self::$instance->_write($level, $msg);
    }
}
