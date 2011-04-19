#!/usr/bin/env php
<?php
set_include_path(__DIR__ . '/../:' .  get_include_path());

require_once 'Lagged/Munin.php';

/**
 * Example datapoint.
 */
class FreeMemory extends \Lagged\Munin\DataPoint
{
}

/**
 * Plugin
 */
class FreeMemoryPlugin extends \Lagged\Munin\Plugin
{
    public function getMemory()
    {
        $cmd = 'free -m -t|grep Total|awk \'{print $4}\'';
        return trim(shell_exec($cmd));
    }

    protected function setUpDataPoints()
    {
        $freeMemory         = new FreeMemory;
        $freeMemory->label  = 'free memory';
        $freeMemory->min    = 0;
        $freeMemory->colour = 'FF0000';

        $this->dataPoints['freememory'] = $freeMemory;
    }
}

$plugin = new FreeMemoryPlugin(
    'My first munin plugin which shows free memory',
    'Systems',
    'free memory',
    'minute',
    'yes'
);
$plugin->setAutoConf(true);

if (isset($argv[1])) {
    switch ($argv[1]) {
    case 'config':
        echo $plugin;
        break;
    case 'autoconf':
        echo $plugin->getAutoConf() . "\n";
        break;
    }
    exit(0);
}

$plugin->setValue('freememory', $plugin->getMemory());
echo $plugin->getValues();
exit(0);
