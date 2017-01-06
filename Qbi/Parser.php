<?php

namespace Qbi;

class Parser
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
     * @param \Qbi\Config         $config
     * @param \Qbi\File           $file
     * @param \Qbi\Console\Output $output
     * @param \Qbi\Console\Input  $input
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
    }
}
