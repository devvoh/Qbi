<?php

namespace Qbi\Plugins;

use Qbi\Command;
use Qbi\Communicator;
use Qbi\Config;
use Qbi\Error;
use Qbi\File;
use Qbi\Hook;

abstract class Base
{
    protected $config;
    protected $file;
    protected $communicator;
    protected $hook;
    protected $command;

    /** @var string */
    protected $keyword = '';

    /** @var bool */
    protected $acceptsArguments = false;

    /** @var string */
    protected $help = '';

    /** @var string */
    protected $matchString;

    public function __construct(
        Config       $config,
        File         $file,
        Communicator $communicator,
        Hook         $hook,
        Command      $command
    ) {
        $this->config       = $config;
        $this->file         = $file;
        $this->communicator = $communicator;
        $this->hook         = $hook;
        $this->command      = $command;
    }

    abstract public function init();

    protected function setKeyword(string $keyword)
    {
        $this->keyword = $keyword;
    }

    public function getKeyword() : string
    {
        return $this->keyword;
    }

    protected function setAcceptsArguments(bool $acceptsArguments)
    {
        $this->acceptsArguments = $acceptsArguments;
    }

    public function doesAcceptArguments() : bool
    {
        return $this->acceptsArguments;
    }

    protected function setHelp(string $help)
    {
        $this->help = $help;
    }

    public function getHelp() : string
    {
        return $this->help;
    }

    protected function setMatchString(string $string)
    {
        $this->matchString = $string;
    }

    public function getMatchString() : string
    {
        return $this->matchString;
    }

    public function matchesWithString(string $string) : bool
    {
        $partStart = substr($string, 0, strlen($this->matchString));
        $partEnd   = substr($string, -strlen($this->matchString));

        return ($partStart === $this->matchString || $partEnd === $this->matchString);
    }

    protected $intervalStart = 0;
    protected $intervalLastStarted = 0;
    protected $interval = 0;
    public function setIntervalInSeconds(int $seconds)
    {
        $this->intervalStart = time();
        $this->interval      = $seconds;
    }

    public function setIntervalInMinutes(int $minutes)
    {
        $this->setIntervalInSeconds($minutes * 60);
    }

    public function setIntervalInHours(int $hours)
    {
        $this->setIntervalInHours($hours * 60);
    }

    public function checkInterval() : bool
    {
        $diff = time() - $this->intervalStart;

        if (time() == $this->intervalLastStarted) {
            return false;
        }

        $shouldRun = ($diff % $this->interval === 0);

        if ($shouldRun) {
            $this->intervalLastStarted = time();
        }
        return $shouldRun;
    }

    protected function setAction(callable $action)
    {
        $className = get_class($this);

        $type = null;
        if (strpos($className, 'Commands') !== false) {
            $type = 'commands';
        } elseif (strpos($className, 'Tasks') !== false) {
            $type = 'tasks';
        } elseif (strpos($className, 'Triggers') !== false) {
            $type = 'triggers';
        }

        if (!$type) {
            throw new Error("Command '{$className}' is not of a valid type.");
        }

        $hook_event = "qbi_plugin_{$type}_{$this->keyword}";

        $this->hook->into($hook_event, $action);
    }
}
