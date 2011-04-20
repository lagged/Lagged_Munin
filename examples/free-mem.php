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
use \Lagged\Munin\CliRunner as CliRunner;

/**
 * Example datapoint.
 */
class FreeMemory extends DataPoint
{
}

/**
 * Example plugin.
 */
class FreeMemoryPlugin extends Plugin
{
    public function process()
    {
        $cmd = 'free -m -t|grep Total|awk \'{print $4}\'';
        $mem = trim(shell_exec($cmd));

        $this->setValue('freememory', $mem);
    }

    protected function setUpDataPoints()
    {
        $freeMemory         = new FreeMemory;
        $freeMemory->label  = 'free memory';
        $freeMemory->min    = 0;
        $freeMemory->colour = 'FF0000';

        $this->dataPoints[$freeMemory->getName()] = $freeMemory;
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

exit(CliRunner::handle($plugin));
