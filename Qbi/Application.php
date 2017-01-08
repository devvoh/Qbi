<?php

namespace Qbi;

class Application
{
    const VERSION     = "0.1.0";
    const QBI_PREFIX  = "[QBI]";
    const QBI_COMMAND = "q";

    protected $config;
    protected $supervisor;
    protected $plugin;
    protected $parser;
    protected $output;
    protected $input;

    public function __construct(
        Config         $config,
        Supervisor     $supervisor,
        Plugin         $plugin,
        Parser         $parser,
        Console\Output $output,
        Console\Input  $input
    ) {
        $this->config     = $config;
        $this->supervisor = $supervisor;
        $this->plugin     = $plugin;
        $this->parser     = $parser;
        $this->output     = $output;
        $this->input      = $input;

        $this->parser->init();
    }

    public function start() : Application
    {
        $this->output->writelns([
            'Qbi version ' . self::VERSION . ' - Minecraft Server Monitor',
            '--------------------------------------------',
            'Starting Supervisor...',
        ]);

        $this->supervisor->start();

        $this->output->writeln('Loading Plugins:');
        $this->plugin->init();

        $this->output->writeln("Monitoring...");
        for (;;) {
            if (!$this->supervisor->checkScreenStatus() || !$this->supervisor->checkServerStatus()) {
                $this->output->writeln("<error>Server has gone offline, restarting.</error>");
                $this->supervisor->restart();
            }

            usleep(250000);

            $lines = $this->parser->go();
            foreach ($lines as $line) {
                // See if the line is a command, if so, attempt to handle it.
                if ($line->isCommand()) {
                    $this->plugin->handleLineAsCommand($line);
                }
                // If it's not player chat, it might be something we can trigger on
                if (!$line->isPlayerChat()) {
                    $this->plugin->handleLineAsTrigger($line);
                }
            }

            $this->plugin->runTasks();

            // Handle Tasks here. Since they're not based on user input but on time and circumstances, we'll need to
            // make sure they get handled when they're supposed to. Make them run in the background, perhaps?
        }

        return $this;
    }
}
