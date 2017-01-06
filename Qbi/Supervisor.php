<?php

namespace Qbi;

class Supervisor
{
    /**
     * @var \Qbi\Config
     */
    protected $config;

    /**
     * @var \Qbi\File
     */
    protected $file;

    /**
     * @var \Qbi\Console\Output
     */
    protected $output;

    /**
     * @var \Qbi\Console\Input
     */
    protected $input;

    /**
     * @var bool
     */
    protected $screenStatus = false;

    /**
     * @var bool
     */
    protected $serverStatus = false;

    /**
     * @param \Qbi\Config         $config
     * @param \Qbi\File           $file
     * @param \Qbi\Console\Output $output
     * @param \Qbi\Console\Input $input
     */
    public function __construct(
        \Qbi\Config         $config,
        \Qbi\File           $file,
        \Qbi\Console\Output $output,
        \Qbi\Console\Input  $input
    ) {
        $this->config = $config;
        $this->file   = $file;
        $this->output = $output;
        $this->input  = $input;

        $this->output->setPrefix('[SUPERVISOR]');
    }

    public function start()
    {
        $this->output->newline();

        $this->checkCurrentStatus();
        $this->output->writeln('Initial status of services:');

        $this->output->writePrefix();
        $this->output->write('[' . ($this->screenStatus ? '<correct>✓</correct>' : '<error>✗</error>') . '] Screen ');
        $this->output->write('[' . ($this->serverStatus ? '<correct>✓</correct>' : '<error>✗</error>') . '] Server ');
        $this->output->newline();
    }

    protected function checkCurrentStatus()
    {
        $this->checkScreenStatus();
        $this->checkServerStatus();
    }

    protected function checkScreenStatus() : bool
    {
        $screenName = $this->config->get('screen.name');

        exec("screen -ls | grep '{$screenName}'", $return, $exitCode);

        // grep exit code 0 = line found, 1 = no line found, 2 = error
        $this->screenStatus = ($exitCode === 0);

        return $this->screenStatus;
    }

    protected function checkServerStatus() : bool
    {
        $this->serverStatus = $this->file->exists("server.status") && $this->file->getContent("server.status") === "ON";
        return $this->serverStatus;
    }
}
