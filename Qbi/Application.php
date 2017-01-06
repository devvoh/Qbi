<?php

namespace Qbi;

class Application
{
    /**
     * @var \Qbi\Config
     */
    protected $config;

    /**
     * @var \Qbi\Supervisor
     */
    protected $supervisor;

    /**
     * @var \Qbi\Parser
     */
    protected $parser;

    /**
     * @var \Qbi\Console\Output
     */
    protected $output;

    /**
     * @var \Qbi\Console\Input
     */
    protected $input;

    /**
     * @var string
     */
    const VERSION = '0.1.0';

    /**
     * @param \Qbi\Config         $config
     * @param \Qbi\Supervisor     $supervisor
     * @param \Qbi\Parser         $parser
     * @param \Qbi\Console\Output $output
     * @param \Qbi\Console\Input  $input
     */
    public function __construct(
        \Qbi\Config         $config,
        \Qbi\Supervisor     $supervisor,
        \Qbi\Parser         $parser,
        \Qbi\Console\Output $output,
        \Qbi\Console\Input  $input
    ) {
        $this->config     = $config;
        $this->supervisor = $supervisor;
        $this->parser     = $parser;
        $this->output     = $output;
        $this->input      = $input;

        $this->output->setPrefix('[QBI]');
    }

    public function start() : \Qbi\Application
    {
        $this->output->clear();

        $this->output->writeln('Qbi version ' . self::VERSION . ' - mc Server Monitor');
        $this->output->writeln('-------------------------------------');

        $this->output->writeln('Starting Supervisor...');

        $this->supervisor->start();

//        for (;;) {
//
//        }

        return $this;
    }
}
