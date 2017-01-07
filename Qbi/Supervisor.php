<?php

namespace Qbi;

class Supervisor
{
    /**
     * @var \Qbi\System
     */
    protected $system;

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
     * @var \Qbi\Parser
     */
    protected $parser;

    /**
     * @var bool
     */
    protected $screenStatus = false;

    /**
     * @var bool
     */
    protected $serverStatus = false;

    /**
     * @var bool
     */
    protected $serverStarting = false;

    public function __construct(
        \Qbi\System         $system,
        \Qbi\Config         $config,
        \Qbi\File           $file,
        \Qbi\Console\Output $output,
        \Qbi\Console\Input  $input,
        \Qbi\Parser         $parser
    ) {
        $this->system = $system;
        $this->config = $config;
        $this->file   = $file;
        $this->output = $output;
        $this->input  = $input;
        $this->parser = $parser;
    }

    public function start()
    {
        $this->output->newline();

        $this->checkCurrentStatus();
        $this->output->writeln('Initial status of services:');

        // If server says OK but screen does not, we're out of sync. Set the server to OFF
        if (!$this->screenStatus && $this->serverStatus) {
            $this->file->putContent("server.status", "OFF");
            $this->serverStatus = false;
        }

        $this->output->writePrefix();
        $this->output->write('[' . ($this->screenStatus ? '<correct>✓</correct>' : '<error>✗</error>') . '] Screen ');
        $this->output->write('[' . ($this->serverStatus ? '<correct>✓</correct>' : '<error>✗</error>') . '] Server ');
        $this->output->newline();

        if (!$this->screenStatus) {
            $this->startScreen();
        }
        if (!$this->serverStatus) {
            $this->startServer();
        }
    }

    protected function checkCurrentStatus()
    {
        $this->checkScreenStatus();
        $this->checkServerStatus();
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
        $this->serverStatus = $this->file->exists("server.status") && $this->file->getContent("server.status") === "ON";
        return $this->serverStatus;
    }

    public function restart()
    {
        $this->output->newline();
        if (!$this->checkScreenStatus()) {
            $this->startScreen();
        }
        $this->startServer();
    }

    protected function startScreen()
    {
        $screenName = $this->config->get('screen.name');
        $this->output->writePrefix();
        $this->output->write("Starting screen '{$screenName}'...");

        $this->system->run("screen -Sdm {$screenName}");

        $this->screenStatus = true;
        $this->output->write(" <correct>✓</correct>");
        $this->output->newline();
    }

    protected function endScreen()
    {
        $screenName = $this->config->get('screen.name');
        $this->output->writePrefix();
        $this->output->write("Ending screen '{$screenName}'...");

        $this->system->run("screen -S {$screenName} -X quit");

        $this->screenStatus = false;
        $this->output->write(" <correct>✓</correct>");
        $this->output->newline();
    }

    protected function startServer()
    {
        $this->output->writePrefix();
        $this->output->write("Starting server");
        $command = [];

        // Check whether the server.location and server.jar actually work out
        $serverLocation = $this->config->get('server.location');
        $serverJar      = $this->config->get('server.jar');

        $serverPath     = realpath(__DIR__ . '/..') . '/' . $serverLocation . '/' . $serverJar;

        if (!$this->file->exists($serverPath)) {
            $this->output->error("Server could not be found at '{$serverPath}'");
        }

        $this->file->delete($serverLocation . '/logs/latest.log');
        $this->parser->init();

        $this->file->putContent("server.status", "ON");

        $command[] = 'java';
        $command[] = '-Xmx' . $this->config->get('server.xmx') . 'M';
        $command[] = '-Xms' . $this->config->get('server.xms') . 'M';
        $command[] = '-jar';
        $command[] = $serverJar;
        $command[] = 'nogui';
        // We need to let the command we're sending to screen write OFF to server.status, for which we need the
        // current working directory
        $command[] = ';echo "OFF" > ' . getcwd() . '/server.status';

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
    }
}
