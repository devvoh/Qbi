<?php

namespace Qbi;

class File
{
    protected $config;

    public function __construct(
        \Qbi\Config $config
    ) {
        $this->config = $config;
    }

    public function exists(string $file) : bool
    {
        return file_exists($file);
    }

    public function delete(string $file) : bool
    {
        return unlink($file);
    }

    public function create(string $file) : bool
    {
        return touch($file);
    }

    public function getContent(string $file) : string
    {
        if ($this->exists($file)) {
            return trim(file_get_contents($file));
        }
        return '';
    }

    public function putContent(string $file, string $data) : bool
    {
        if ($this->exists($file)) {
            $this->delete($file);
        }
        return $this->appendContent($file, $data);
    }

    public function appendContent(string $file, string $data) : bool
    {
        if (!$this->exists($file)) {
            $this->create($file);
        }
        if (!$this->exists($file)) {
            return false;
        }
        return file_put_contents($file, trim($data), \FILE_APPEND) !== false;
    }

}
