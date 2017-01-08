<?php

namespace Qbi\Parser;

use \Qbi\Application;

class Line
{
    protected $string        = '';
    protected $time          = '';
    protected $serverLabel   = '';
    protected $playerName    = '';
    protected $playerMessage = '';
    protected $commandString = '';

    /**
     * Our toggles
     *
     * @var bool
     */
    protected $serverReady   = false;
    protected $playerChat    = false;
    protected $playerJoining = false;
    protected $playerLeaving = false;
    protected $qbi           = false;
    protected $command       = false;

    public function setString(string $string) : Line
    {
        // Remove the [SERVER] tag to allow the server chat to also trigger Qbi
        $string = str_replace("[Server] ", "", $string);

        // Extract the timestamp and trim the string
        $string = $this->extractTimeAndTrim($string);

        // Check whether it's a statement the server is ready
        $this->checkIsServerReady($string);

        // Extract the label and trim the string
        $string = $this->extractServerLabelAndTrim($string);

        // Store the string so far, this is a clean version
        $this->string = $string;

        // Check and set some booleans to make it easy to figure out what we're dealing with
        $this->checkIsPlayerChat($string);
        $this->checkIsPlayerJoining($string);
        $this->checkIsPlayerLeaving($string);
        $this->checkIsQbi($string);

        if ($this->isPlayerChat() || $this->isPlayerJoining() || $this->isPlayerLeaving()) {
            $this->extractPlayerName($string);
        }
        if ($this->isPlayerChat()) {
            $this->extractPlayerMessage($string);

            $this->checkIsCommand($this->playerMessage);
        }
        if ($this->isCommand()) {
            $this->extractCommand($this->playerMessage);
        }

        return $this;
    }

    public function getTime() : string
    {
        return $this->time;
    }

    public function getServerLabel() : string
    {
        return $this->serverLabel;
    }

    public function getPlayerMessage() : string
    {
        return $this->playerMessage;
    }

    public function getCommandString() : string
    {
        return $this->commandString;
    }

    public function getString() : string
    {
        return $this->string;
    }

    public function getPlayerName() : string
    {
        return $this->playerName;
    }

    public function isServerReady() : bool
    {
        return $this->serverReady;
    }

    public function isPlayerChat() : bool
    {
        return $this->playerChat;
    }

    public function isPlayerJoining() : bool
    {
        return $this->playerJoining;
    }

    public function isPlayerLeaving() : bool
    {
        return $this->playerLeaving;
    }

    public function isQbi() : bool
    {
        return $this->qbi;
    }

    public function isCommand() : bool
    {
        return $this->command;
    }

    protected function checkIsServerReady(string $string) : bool
    {
        $this->serverReady = substr($string, 0, 26) === "[Server thread/INFO]: Done";
        return $this->serverReady;
    }

    protected function checkIsPlayerChat(string $string) : bool
    {
        $this->playerChat = (strpos($string, '<') !== false && strpos($string, '>') !== false);
        return $this->playerChat;
    }

    protected function checkIsPlayerJoining(string $string) : bool
    {
        $this->playerJoining = substr($string, -15) === 'joined the game';
        return $this->playerJoining;
    }

    protected function checkIsPlayerLeaving(string $string) : bool
    {
        $this->playerLeaving = substr($string, -13) === 'left the game';
        return $this->playerLeaving;
    }

    protected function checkIsQbi(string $string) : bool
    {
        $this->qbi = substr($string, 0, 5) === Application::QBI_PREFIX;
        return $this->qbi;
    }

    protected function checkIsCommand(string $string) : bool
    {
        if ($this->isPlayerChat()) {
            $commandString = Application::QBI_COMMAND . ' ';
            $this->command = substr($string, 0, strlen($commandString)) === $commandString;
        }
        if ($this->isCommand()) {
            // Command supersedes playerChat. Though it starts as both, for Qbi it now is just a command.
            $this->playerChat = false;
        }
        return $this->command;
    }

    protected function extractCommand(string $string)
    {
        $commandString = Application::QBI_COMMAND . ' ';
        $this->commandString = str_replace($commandString, "", $string);
    }

    protected function extractPlayerName(string $string)
    {
        if ($this->isPlayerChat()) {
            $almost = explode('>', $string);
            $this->playerName = str_replace('<', '', $almost[0]);
        } elseif ($this->isPlayerJoining()) {
            $this->playerName = str_replace(" joined the game", "", $string);
        } elseif ($this->isPlayerLeaving()) {
            echo 'HELLO';
            $this->playerName = str_replace(" left the game", "", $string);
        }
    }
    protected function extractPlayerMessage(string $string)
    {
        $almost = explode('>', $string);
        $this->playerMessage = trim($almost[1]);
    }

    protected function extractTimeAndTrim(string $string) : string
    {
        $time = substr($string, 0, 10);
        $this->time = substr($time, 1, -1);

        return substr($string, 11);
    }

    protected function extractServerLabelAndTrim(string $string) : string
    {
        $serverLabel = substr($string, 0, 20);
        $this->serverLabel = substr($serverLabel, 15, -1);

        return substr($string, 22);
    }

}