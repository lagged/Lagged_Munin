#!/usr/bin/env php
<?php
/**
 * @author  Till Klampaeckel <till@php.net>
 * @license http://www.opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * @desc Set include_path, assuming this is run from a checkout.
 */
set_include_path(__DIR__ . '/../:' .  get_include_path());

require_once 'Lagged/Munin.php';

use \Lagged\Munin\DataPoint as DataPoint;
use \Lagged\Munin\Plugin as Plugin;

/**
 * Example datapoint.
 */
class CurrentLoad extends DataPoint
{
}

/**
 * Example plugin.
 */
class LoadPlugin extends Plugin
{
    public function getLoad()
    {
        $cmd = 'uptime|awk \'{print $10}\'';
        return substr(trim(shell_exec($cmd)), 0, -1);
    }

    protected function setUpDataPoints()
    {
        // implemented, but not used
    }
}

$plugin = new LoadPlugin(
    'My second munin plugin which shows the current load',
    'Systems',
    'load',
    'minute',
    'yes'
);
$plugin->setAutoConf(true);

$load        = new CurrentLoad;
$load->label = 'current load';
$load->min   = 0;
$load->type  = 'ABSOLUTE';

$plugin->addDataPoint($load);


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

$plugin->setValue($load->getName(), $plugin->getLoad());
echo $plugin->getValues();
exit(0);
