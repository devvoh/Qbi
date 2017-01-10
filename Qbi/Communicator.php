<?php

namespace Qbi;

class Communicator
{
    protected $system;

    public function __construct(
        System $system
    ) {
        $this->system = $system;

        $this->prefix = Application::QBI_PREFIX;
    }

    public function say(string $message) : Communicator
    {
        $this->system->runOnScreen("/say {$this->prefix} {$message}");
        return $this;
    }

    public function tell(string $playerName, string $message) : Communicator
    {
        $this->system->runOnScreen("/tell {$playerName} {$this->prefix} {$message}");
        return $this;
    }

    public function tp(string $playerName, int $x, int $y, int $z) : Communicator
    {
        $this->system->runOnScreen("/tp {$playerName} {$x} {$y} {$z}");
        return $this;
    }

    public function tellRaw(string $playerName, string $message) : Communicator
    {
        $json = $this->jsonEncode([
            "text" => $message,
        ]);
        $this->system->runOnScreen("/tellraw {$playerName} {$json}");
        return $this;
    }

    public function tellRawWithPrefix(string $playerName, string $message) : Communicator
    {
        $this->tellRaw($playerName, "{$this->prefix} {$message}");
        return $this;
    }

    public function title(
        string $playerName,
        string $type = 'title',
        string $message,
        string $color = 'white',
        bool $bold = false,
        bool $italic = false
    ) : Communicator {
        $json = $this->jsonEncode([
            "text"   => $message,
            "color"  => $color,
            "bold"   => $bold ? "true" : "false",
            "italic" => $italic ? "true" : "false",
        ]);
        $this->system->runOnScreen("/title {$playerName} {$type} {$json}");
        return $this;
    }

    /**
     * Double-encoding the array makes sure our double quotes are double-escaped. This is necessary to
     * send it through the screen command.
     *
     * @param $array
     *
     * @return string
     */
    protected function jsonEncode($array) : string {
        foreach ($array as &$value) {
            $value = str_replace("'", "’", $value);
        }
        $json = json_encode($array);
        return $json;
    }
}
