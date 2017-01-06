<?php

namespace Qbi\Console;

class Output
{
    protected $prefix = '';

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function write($string)
    {
        echo $string;
        return $this;
    }

    public function newline($count = 1)
    {
        echo str_repeat(PHP_EOL, $count);
        return $this;
    }

    public function writeln($string)
    {
        if ($this->prefix) {
            $this->write($this->prefix . ' ');
        }
        $this->write($string);
        $this->newline();
        return $this;
    }
}