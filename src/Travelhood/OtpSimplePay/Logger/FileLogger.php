<?php

namespace Travelhood\OtpSimplePay\Logger;

use Psr\Log\AbstractLogger;
use RuntimeException;

class FileLogger extends AbstractLogger
{
    const LEVELS = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];

    protected $_path;
    protected $_level;
    protected $_prefix;
    protected $_handle;

    public function __construct($path, $level = 'debug', $prefix='')
    {
        if (!is_file($path)) {
            touch($path);
            chmod($path, 0666);
        }
        $this->_path = $path;
        $this->_level = array_search(strtolower($level), self::LEVELS);
        $this->_prefix = $prefix;
        $this->_handle = fopen($path, 'a+');
        if (!is_resource($this->_handle)) {
            throw new RuntimeException('Failed to open handle for logging to file: ' . $path);
        }
    }

    public function setPrefix($prefix)
    {
        $this->_prefix = $prefix;
        return $this;
    }

    public function __destruct()
    {
        if (is_resource($this->_handle)) {
            flock($this->_handle, LOCK_UN);
            fclose($this->_handle);
        }
    }

    public function log($level, $message, array $context = [])
    {
        $idx = array_search(strtolower($level), self::LEVELS);
        if ($idx < $this->_level) {
            return;
        }
        if (!is_resource($this->_handle)) {
            throw new RuntimeException('No open handle to log to');
        }
        $formatted = '[' . date('Y-m-d H:i:s') . '] [' . $level . '] ' . $this->_prefix . $message;
        if (count($context) > 0) {
            $formatted .= ' ' . json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        flock($this->_handle, LOCK_EX);
        $put = fputs($this->_handle, trim($formatted) . PHP_EOL);
        flock($this->_handle, LOCK_UN);
        if (!$put) {
            throw new RuntimeException('Failed to write to log file');
        }
    }
}