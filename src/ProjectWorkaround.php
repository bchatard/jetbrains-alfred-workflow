<?php


class ProjectWorkaround
{

    private static $mapping = [
        'idea'     => 'IntelliJ IDEA',
        'pstorm'   => 'PhpStorm',
        'phpstorm' => 'PhpStorm',
        'wstorm'   => 'WebStorm',
        'webstorm' => 'WebStorm',
        // complete here
    ];

    private $jetbrainsApp;

    public function __construct($jetbrainsApp)
    {
        $this->jetbrainsApp = $jetbrainsApp;
    }

    private function mapBinNameToAppName()
    {
        $bin = basename($this->jetbrainsApp);
        if (array_key_exists($bin, static::$mapping)) {
            return static::$mapping[$bin];
        }
        throw new WorkflowException("WA - Can't find application for '{$this->jetbrainsApp}'");
    }

    public function searchAppAndConfigPath()
    {
        $appName = $this->mapBinNameToAppName();

        return [
            'config' => "{$_SERVER['HOME']}/Library/Preferences/{$appName}{$_SERVER['jb_app_version']}",
            'app'    => "{$_SERVER['HOME']}/Applications/JetBrains Toolbox/{$appName}.app",
        ];
    }

}
