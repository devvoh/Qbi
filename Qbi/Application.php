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
    const VERSION = "0.1.0";

    /**
     * @var string
     */
    const QBI_PREFIX = "[QBI]";

    /**
     * @var string
     */
    const QBI_COMMAND = "q";

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

        $this->parser->init();
        $this->output->setPrefix('[QBI]');
    }

    public function start() : \Qbi\Application
    {
        $this->output->clear();

        $prefix = $this->output->getPrefix();
        $this->output->setPrefix('');

        $this->output->writeln('Qbi version ' . self::VERSION . ' - Minecraft vanilla Server Monitor');
        $this->output->writeln('----------------------------------------------------');
        $this->output->newline();

        $this->output->setPrefix($prefix);

        $this->output->writeln('Starting Supervisor...');

        $this->supervisor->start();

        $this->output->newline();

        $this->output->writePrefix();
        $this->output->write('Server is online, monitoring log ');
        $this->output->startSpinner();
        for (;;) {
            if (!$this->supervisor->checkScreenStatus() || !$this->supervisor->checkServerStatus()) {
                $this->output->newline();
                $this->output->writeln("Server has gone offline, restarting.");
                $this->supervisor->restart();
            }

            usleep(250000);
            $this->output->animateSpinner();

            $lines = $this->parser->go();
            foreach ($lines as $line) {
                if ($line->isCommand()) {
                    // Handle commands
                }
                if ($line->isPlayerJoining()) {
                    // Handle player joining
                }
                if ($line->isPlayerLeaving()) {
                    // Handle player leaving
                }
                if ($line->isPlayerChat()) {
                    // Handle player chatting?
                }
                if ($line->isQbi()) {
                    // Ignore
                }
                if ($line->isUnimportant()) {
                    // Ignore
                }
            }

            // Handle Tasks here
        }
        $this->output->endSpinner();

        return $this;
    }
}
