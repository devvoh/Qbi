<?php

namespace Qbi;

class Config
{
    protected $options = [
        'env' => [
            'base_path' => __DIR__,
        ],
        'screen' => [
            'name' => 'server_screen',
        ],
        'server' => [
            'location' => 'mc-server',
            'jar_name' => 'minecraft_server.jar',
            'xmx'      => 1024,
            'xms'      => 1024,
            'nogui'    => true,
        ],
        'http' => [
            'generate' => true,
            'page' => [
                'target_dir' => '/var/www/html/qbi.html',
                'title'      => 'Qbi ' . Application::VERSION . ' status page',
            ],
        ],
    ];

    /**
     * @param string $key
     *
     * @return array|mixed|null
     */
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
