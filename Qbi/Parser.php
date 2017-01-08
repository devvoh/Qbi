<?php

namespace Qbi;

class Parser
{
    protected $system;
    protected $config;
    protected $file;
    protected $output;
    protected $input;
    protected $logLocation;

    /** @var int */
    protected $lastLineParsed = 0;

    /**
     * @param System         $system
     * @param Config         $config
     * @param File           $file
     * @param Console\Output $output
     * @param Console\Input  $input
     */
    public function __construct(
        System         $system,
        Config         $config,
        File           $file,
        Console\Output $output,
        Console\Input  $input
    ) {
        $this->system = $system;
        $this->config = $config;
        $this->file   = $file;
        $this->output = $output;
        $this->input  = $input;

        $this->logLocation = $this->config->get('server.location') . '/logs/latest.log';
    }

    public function init() : Parser
    {
        $lines = $this->getLines();
        $this->lastLineParsed = count($lines);
        return $this;
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
            $line = DI::create(Parser\Line::class);
            $line->setString($lineString);

            $parsedLines[] = $line;

            // Up the last line parsed ALWAYS
            $this->lastLineParsed++;
        }

        return $parsedLines;
    }

    /**
     * @return string[]
     */
    public function getLines() : array
    {
        if (!$this->file->exists($this->logLocation)) {
            return [];
        }

        $lines = $this->file->getContent($this->logLocation);
        return explode(PHP_EOL, $lines);
    }
}
