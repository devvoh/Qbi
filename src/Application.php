<?php

namespace Qbi;

class Application
{
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
     * @param \Qbi\Supervisor     $supervisor
     * @param \Qbi\Parser         $parser
     * @param \Qbi\Console\Output $output
     * @param \Qbi\Console\Input  $input
     */
    public function __construct(
        \Qbi\Supervisor     $supervisor,
        \Qbi\Parser         $parser,
        \Qbi\Console\Output $output,
        \Qbi\Console\Input  $input
    ) {
        $this->supervisor = $supervisor;
        $this->parser     = $parser;
        $this->output     = $output;
        $this->input      = $input;
    }

    public function start()
    {
        $this->output->writeln('Qbi 0.1.0 - mc Server Monitor');
        $this->output->writeln('------------------------------------');

        $this->output->writeln('Starting Supervisor...');
        $this->supervisor->start();

        for (;;) {

        }
    }
}