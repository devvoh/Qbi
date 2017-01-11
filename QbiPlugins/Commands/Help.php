<?php

namespace QbiPlugins\Commands;

class Help
{
    public function init(\Qbi\PluginManager $pluginManager)
    {
        $pluginManager->addCommand(
            'Help',
            ['help'],
            'Information about Qbi.',
            function(\Qbi\Parser\Line $line) use ($pluginManager) {
                $commands = $pluginManager->getCommands();

                $pluginManager->getCommunicator()->tellRaw($line->getPlayerName(), "Qbi " . \Qbi\Application::VERSION . " -- Help");
                $pluginManager->getCommunicator()->tellRaw($line->getPlayerName(), str_repeat('-', 46));
                $pluginManager->getCommunicator()->tellRaw($line->getPlayerName(), "Qbi is a server supervisor and command/trigger/task");
                $pluginManager->getCommunicator()->tellRaw($line->getPlayerName(), "extending wrapper.");
                $pluginManager->getCommunicator()->tellRaw($line->getPlayerName(), str_repeat('-', 46));

                foreach ($commands as $command) {
                    $commandString = implode(' | ', $command['keywords']);
                    $pluginManager->getCommunicator()->tellRaw(
                        $line->getPlayerName(),
                        "q $commandString"
                    );
                    // sleep just a little so as to not overwhelm the server (odd but necessary)
                    usleep(10000);
                }
            }
        );
    }
}
