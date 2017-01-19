<?php

namespace QbiPlugins\Tasks;

class CleanServerOutput
{
    public function init(\Qbi\PluginManager $pluginManager)
    {
        $pluginManager->addTask(
            'CleanServerOutput',
            new \DateTime('yesterday 03:00:00'), // Time 3am, and yesterday so it'll run on first check.
            \Qbi\Tool::secondsFromDays(1), // Run every day.
            function () use ($pluginManager) {
                $serverOutputLocation = getcwd() . '/storage/server.output';
                $file = $pluginManager->getFile();
                if ($file->exists($serverOutputLocation)) {
                    $file->put($serverOutputLocation, '');
                }
            }
        );
    }
}
