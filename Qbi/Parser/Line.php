<?php

namespace Qbi\Parser;

use Qbi\Application;

class Line
{
    protected $string = '';
    protected $time = '';
    protected $serverLabel = '';
    protected $playerName = '';
    protected $playerChat = '';
    protected $command = '';

    protected $isServerReady = false;
    protected $isPlayerChat = false;
    protected $isPlayerJoining = false;
    protected $isPlayerLeaving = false;
    protected $isQbi = false;
    protected $isCommand = false;
    protected $isUnimportant = false;

    public function __construct(
        // Inevitable
    ) {
    }

    public function setString(string $string) : \Qbi\Parser\Line
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

        if ($this->isPlayerChat() || $this->isPlayerJoining()) {
            $this->extractPlayerName($string);
        }
        if ($this->isPlayerChat()) {
            $this->extractPlayerChat($string);

            $this->checkIsCommand($this->playerChat);
        }
        if ($this->isCommand()) {
            $this->extractCommand($this->playerChat);
        }

        $this->checkIsUnimportant();

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

    public function getPlayerChat() : string
    {
        return $this->playerChat;
    }

    public function getCommand() : string
    {
        return $this->command;
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
        return $this->isServerReady;
    }

    public function isPlayerChat() : bool
    {
        return $this->isPlayerChat;
    }

    public function isPlayerJoining() : bool
    {
        return $this->isPlayerJoining;
    }

    public function isPlayerLeaving() : bool
    {
        return $this->isPlayerLeaving;
    }

    public function isQbi() : bool
    {
        return $this->isQbi;
    }

    public function isCommand() : bool
    {
        return $this->isCommand;
    }

    public function isUnimportant() : bool
    {
        return $this->isUnimportant;
    }

    protected function checkIsServerReady(string $string) : bool
    {
        $this->isServerReady = substr($string, 0, 26) === "[Server thread/INFO]: Done";
        return $this->isServerReady;
    }

    protected function checkIsPlayerChat(string $string) : bool
    {
        $this->isPlayerChat = (strpos($string, '<') !== false && strpos($string, '>') !== false);
        return $this->isPlayerChat;
    }

    protected function checkIsPlayerJoining(string $string) : bool
    {
        $this->isPlayerJoining = substr($string, -15) === 'joined the game';
        return $this->isPlayerJoining;
    }

    protected function checkIsPlayerLeaving(string $string) : bool
    {
        $this->isPlayerLeaving = substr($string, -13) === 'left the game';
        return $this->isPlayerLeaving;
    }

    protected function checkIsQbi(string $string) : bool
    {
        $this->isQbi = substr($string, 0, 5) === \Qbi\Application::QBI_PREFIX;
        return $this->isQbi;
    }

    protected function checkIsCommand(string $string) : bool
    {
        if ($this->isPlayerChat()) {
            $commandString = \Qbi\Application::QBI_COMMAND . ' ';
            $this->isCommand = substr($string, 0, strlen($commandString)) === $commandString;
        }
        if ($this->isCommand()) {
            // Command supersedes playerChat. Though it starts as both, for Qbi it now is just a command.
            $this->isPlayerChat = false;
        }
        return $this->isCommand;
    }

    protected function checkIsUnimportant() : bool
    {
        $this->isUnimportant = !(
            $this->isServerReady()
            || $this->isPlayerJoining()
            || $this->isPlayerLeaving()
            || $this->isCommand()
        );
        return $this->isUnimportant;
    }

    protected function extractCommand(string $string)
    {
        $commandString = \Qbi\Application::QBI_COMMAND . ' ';
        $this->command = str_replace($commandString, "", $string);
    }

    protected function extractPlayerName(string $string)
    {
        if ($this->isPlayerChat()) {
            $almost = explode('>', $string);
            $this->playerName = str_replace('<', '', $almost[0]);
        } elseif ($this->isPlayerJoining()) {
            $this->playerName = str_replace(" joined the game", "", $string);
        } elseif ($this->isPlayerLeaving()) {
            $this->playerName = str_replace(" left the game", "", $string);
        }
    }
    protected function extractPlayerChat(string $string)
    {
        $almost = explode('>', $string);
        $this->playerChat = trim($almost[1]);
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