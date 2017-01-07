<?php

namespace Qbi;

class Parser
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
     * @var string
     */
    protected $logLocation;

    /**
     * @var int
     */
    protected $lastLineParsed = 0;

    /**
     * @param \Qbi\System         $system
     * @param \Qbi\Config         $config
     * @param \Qbi\File           $file
     * @param \Qbi\Console\Output $output
     * @param \Qbi\Console\Input  $input
     */
    public function __construct(
        \Qbi\System         $system,
        \Qbi\Config         $config,
        \Qbi\File           $file,
        \Qbi\Console\Output $output,
        \Qbi\Console\Input  $input
    ) {
        $this->system = $system;
        $this->config = $config;
        $this->file   = $file;
        $this->output = $output;
        $this->input  = $input;

        $this->logLocation = $this->config->get('server.location') . '/logs/latest.log';
    }

    public function init()
    {
        $lines = $this->getLines();
        $this->lastLineParsed = count($lines);
    }

    /**
     * @return \Qbi\Parser\Line[]
     */
    public function go() : array
    {
        $lines = $this->getLines();

        // We only want the lines we consider to be new
        $lines = array_slice($lines, $this->lastLineParsed);

        $linesNew = count($lines);

        $parsedLines = [];
        foreach ($lines as $lineString) {
            /** @var \Qbi\Parser\Line $line */
            $line = \Qbi\DI::create(\Qbi\Parser\Line::class);
            $line->setString($lineString);

            $parsedLines[] = $line;

            // Up the last line parsed ALWAYS
            $this->lastLineParsed++;
        }

        return $parsedLines;
    }

    public function getLines() : array
    {
        if (!$this->file->exists($this->logLocation)) {
            return [];
        }

        $lines = $this->file->getContent($this->logLocation);
        return explode(PHP_EOL, $lines);
    }
}
