<?php

namespace Qbi\Console;

class Output
{
    protected $prefix;

    protected $spinnerFrames = ['⠁', '⠂', '⠄', '⡀', '⢀', '⠠', '⠐', '⠈'];

    protected $spinnerPosition = -1;

    protected $tagSets = [
        [
            'tags' => ['default'],
            'style' => "0;0",
        ],
        [
            'tags' => ['red', 'error'],
            'style' => "0;31",
        ],
        [
            'tags' => ['green', 'correct'],
            'style' => "0;32",
        ],
        [
            'tags' => ['yellow', 'info'],
            'style' => "0;33",
        ],
        [
            'tags' => ['blue'],
            'style' => "0;34",
        ],
        [
            'tags' => ['magenta'],
            'style' => "0;35",
        ],
        [
            'tags' => ['cyan'],
            'style' => "0;36",
        ],
    ];

    public function clear() : \Qbi\Console\Output
    {
        system('clear');
        return $this;
    }

    public function setPrefix(string $prefix) : \Qbi\Console\Output
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function getPrefix() : string
    {
        return $this->prefix;
    }

    public function clearPrefix() : \Qbi\Console\Output
    {
        $this->prefix = null;
        return $this;
    }

    public function writePrefix() : \Qbi\Console\Output
    {
        if ($this->getPrefix() !== '') {
            $this->write($this->getPrefix() . ' ');
        }
        return $this;
    }

    public function write(string $string) : \Qbi\Console\Output
    {
        $string = $this->parseTags($string);
        echo $string;
        return $this;
    }

    public function newline(int $count = 1) : \Qbi\Console\Output
    {
        echo str_repeat(PHP_EOL, $count);
        return $this;
    }

    public function writeln(string $string) : \Qbi\Console\Output
    {
        $this->writePrefix();
        $this->write($string);
        $this->newline();
        return $this;
    }

    public function error(string $string)
    {
        $this->clearPrefix();
        $this->newline(2);
        $this->writeln("<error>[ERROR]</error> {$string}");
        $this->newline();
        die();
    }

    public function parseTags(string $string) : string
    {
        foreach ($this->tagSets as $tagSet) {
            foreach ($tagSet['tags'] as $tag) {
                if ($this->findTag($string, $tag)) {
                    $string = $this->replaceTag($string, $tag, $tagSet['style']);
                }
            }
        }
        return $string;
    }

    public function findTag(string $string, string $tag) : bool
    {
        return strpos($string, "<{$tag}>") !== false;
    }

    public function replaceTag(string $string, string $tag, string $style) : string
    {
        $string = str_replace("<{$tag}>", "\033[{$style}m", $string);
        $string = str_replace("</{$tag}>", "\033[0m", $string);

        return $string;
    }

    public function startSpinner()
    {
        $this->animateSpinner();
    }

    public function animateSpinner()
    {
        $this->spinnerPosition++;
        $this->write($this->spinnerFrames[$this->spinnerPosition % count($this->spinnerFrames)]);
        $this->moveCursorBack(1);
    }

    public function endSpinner()
    {
        $this->spinnerPosition = -1;
        $this->moveCursorBack(1);
        $this->write(' ');
        $this->newline();
    }

    public function moveCursorBack($number)
    {
        echo "\033[{$number}D";
    }

    public function moveCursorForward($number)
    {
        echo "\033[{$number}C";
    }
}