<?php

namespace Qbi;

class Supervisor
{
    /**
     * @var string
     */
    protected $prefix = '[SUPERVISOR]';

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
     * @param \Qbi\Console\Output $output
     * @param \Qbi\Console\Input $input
     */
    public function __construct(
        \Qbi\Console\Output $output,
        \Qbi\Console\Input  $input
    ) {
        $this->output = $output;
        $this->input  = $input;

        $this->output->setPrefix($this->prefix);
    }

    public function start()
    {
        $this->output->writeln('Getting current status...');
        $this->checkCurrentStatus();
        $this->output->writeln('Current status of services:');

        $this->output->writeln('- Screen ' . ($this->screenStatus ? 'ON' : 'OFF'));
        $this->output->writeln('- Server ' . ($this->serverStatus ? 'ON' : 'OFF'));
    }

    protected function checkCurrentStatus()
    {
        $this->screenStatus = true;
        $this->serverStatus = true;
    }
}