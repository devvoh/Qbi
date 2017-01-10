<?php

namespace QbiPlugins\Commands;

class Help
{
    public function init(\Qbi\PluginManager $pluginManager)
    {
        $pluginManager->addCommand(
            ['help'],
            'Information about Qbi.',
            function(\Qbi\Parser\Line $line) use ($pluginManager) {
                $commands = $pluginManager->getCommands();

                $maxLength = 36;

                $pluginManager->getCommunicator()->tellRaw($line->getPlayerName(), "Qbi " . \Qbi\Application::VERSION . " -- Help");
                $pluginManager->getCommunicator()->tellRaw($line->getPlayerName(), str_repeat('-', $maxLength));
                $pluginManager->getCommunicator()->tellRaw($line->getPlayerName(), "Qbi is a supervisor. It keeps the server online in case");
                $pluginManager->getCommunicator()->tellRaw($line->getPlayerName(), "it should crash. It also offers some commands.");
                $pluginManager->getCommunicator()->tellRaw($line->getPlayerName(), str_repeat('-', $maxLength));

                foreach ($commands as $command) {
                    $commandString = implode(' | ', $command['keywords']);
                    $pluginManager->getCommunicator()->tellRaw(
                        $line->getPlayerName(),
                        "q {$commandString}"
                    );
                }
            }
        );
    }
}
