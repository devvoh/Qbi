<?php

namespace Qbi;

class Config
{
    protected $options = [
        'screen' => [
            'name' => 'mc_server_screen',
        ],
        'server' => [
            'location' => 'mc-server',
            'jar'      => 'server.jar',
            'xmx'      => 256,
            'xms'      => 256,
        ],
        'http' => [
            'generate' => true,
            'target' => [
                'dir'  => 'html/qbi',
                'file' => 'index.html',
            ],
            'page' => [
                'title' => 'Qbi information page',
            ]
        ],
        'plugins' => [
            'commands' => [
                'Uptime',
                'Spawn',
                'Help',
            ],
            'tasks' => [
                'Midnight',
            ],
            'triggers' => [
                'PlayerJoin',
                'PlayerLeave',
            ],
        ],
        'pluginSettings' => [
            'commands' => [
                'Spawn' => [
                    'x' => -350,
                    'y' => 64,
                    'z' => 214,
                    'message' => '{playerName} panicked and has been teleported to spawn!',
                ],
            ],
        ],
    ];

    public function get(string $key) {
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
        } else {
            $keys = [$key];
        }

        $config = $this->options;
        foreach ($keys as $key) {
            if (isset($config[$key])) {
                $config = $config[$key];
            } else {
                return null;
            }
        }
        return $config;
    }
}
