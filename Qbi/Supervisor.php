<?php

namespace Qbi;

class Supervisor
{
    protected $system;
    protected $config;
    protected $file;
    protected $output;
    protected $input;
    protected $parser;

    /** @var bool */
    protected $screenStatus = false;

    /** @var bool */
    protected $serverStatus = false;

    /** @var bool */
    protected $serverStarting = false;

    public function __construct(
        System         $system,
        Config         $config,
        File           $file,
        Console\Output $output,
        Console\Input  $input,
        Parser         $parser
    ) {
        $this->system = $system;
        $this->config = $config;
        $this->file   = $file;
        $this->output = $output;
        $this->input  = $input;
        $this->parser = $parser;
    }

    public function start() : Supervisor
    {
        $this->output->writeln('Starting Supervisor...');
        $this->checkCurrentStatus();
        $this->output->writeDateIfEnabled();
        $this->output->write('Initial status of services: ');

        // If server says OK but screen does not, we're out of sync. Set the server to OFF
        if (!$this->screenStatus && $this->serverStatus) {
            $this->file->putContent("storage/server.status", "OFF");
            $this->serverStatus = false;
        }

        $this->output->write('[' . ($this->screenStatus ? '<correct>✓</correct>' : '<error>✗</error>') . '] Screen ');
        $this->output->write('[' . ($this->serverStatus ? '<correct>✓</correct>' : '<error>✗</error>') . '] Server ');
        $this->output->newline();

        if (!$this->screenStatus) {
            $this->startScreen();
        }
        if (!$this->serverStatus) {
            $this->startServer();
        }

        return $this;
    }

    protected function checkCurrentStatus() : bool
    {
        $this->checkScreenStatus();
        $this->checkServerStatus();

        return $this->screenStatus && $this->serverStatus;
    }

    public function checkScreenStatus() : bool
    {
        $screenName = $this->config->get('screen.name');

        $result = $this->system->run("screen -ls | grep '{$screenName}'");

        // grep exit code 0 = line found, 1 = no line found, 2 = error
        $this->screenStatus = ($result['exitCode'] === 0);

        return $this->screenStatus;
    }

    public function checkServerStatus() : bool
    {
        $this->serverStatus = $this->file->exists("storage/server.status") && $this->file->getContent("storage/server.status") === "ON";
        return $this->serverStatus;
    }

    public function restart()
    {
        if (!$this->checkScreenStatus()) {
            $this->startScreen();
        }
        $this->startServer();
    }

    protected function startScreen()
    {
        $screenName = $this->config->get('screen.name');
        if (!$screenName) {
            throw new Exception('screen.name is not set in config.json');
        }

        $this->output->writeDateIfEnabled();
        $this->output->write("Starting screen '{$screenName}'...");

        $this->system->run("screen -Sdm {$screenName}");

        $this->screenStatus = true;
        $this->output->write(" [<correct>✓</correct>]");
        $this->output->newline();
    }

    protected function endScreen()
    {
        $screenName = $this->config->get('screen.name');
        if (!$screenName) {
            throw new Exception('screen.name is not set in config.json');
        }

        $this->output->writeDateIfEnabled();
        $this->output->write("Ending screen '{$screenName}'...");

        $this->system->run("screen -S {$screenName} -X quit");

        $this->screenStatus = false;
        $this->output->write(" [<correct>✓</correct>]");
        $this->output->newline();
    }

    protected function startServer()
    {
        $this->output->writeDateIfEnabled();
        $this->output->write("Starting server");
        $command = [];

        // Check whether the server.location and server.jar actually work out
        $serverLocation = $this->config->get('server.location');
        $serverJar      = $this->config->get('server.jar');

        if (!$serverLocation || !$serverJar) {
            throw new Exception('server.location and/or server.jar are not set in config.json');
        }

        $serverPath     = realpath(__DIR__ . '/..') . '/' . $serverLocation . '/' . $serverJar;

        if (!$this->file->exists($serverPath)) {
            throw new Error("Server could not be found at '{$serverPath}'");
        }

        // Delete existing server.output file
        $this->file->delete(getcwd() . '/storage/server.output');

        $this->parser->init();

        $this->file->putContent("storage/server.status", "ON");

        $command[] = 'java';
        $command[] = '-Xmx' . $this->config->get('server.xmx') . 'M';
        $command[] = '-Xms' . $this->config->get('server.xms') . 'M';
        $command[] = '-jar';
        $command[] = $serverJar;
        $command[] = 'nogui';
        // We need to send all output to qbi.buffer so we can read it out
        $command[] = ' > ' . getcwd() . '/storage/server.output';
        // We need to let the command we're sending to screen write OFF to server.status, for which we need the
        // current working directory
        $command[] = ';echo "OFF" > ' . getcwd() . '/storage/server.status';

        $commandString = implode(' ', $command);

        $this->system->runOnScreen("cd {$serverLocation}");
        $this->system->runOnScreen($commandString);

        while (!$this->serverStatus) {
            echo '.';
            $lines = $this->parser->go();

            foreach ($lines as $line) {
                $this->serverStatus = $line->isServerReady();
            }
            usleep(1000000);
        }

        $this->output->write(" <correct>✓</correct>");
        $this->output->newline();
        $this->output->writeln('<correct>Server is online.</correct>');
    }
}
