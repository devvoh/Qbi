<?php

namespace Qbi;

class Config
{
    protected $options = [
        'env' => [
            'base_path' => __DIR__,
        ],
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
            'page' => [
                'target_dir' => '/var/www/html/qbi.html',
                'title'      => 'Qbi ' . Application::VERSION . ' status page',
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
