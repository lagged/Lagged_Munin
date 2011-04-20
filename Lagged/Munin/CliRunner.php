<?php
/**
 * Copyright (c) 2011, Till Klampaeckel
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *  * Redistributions of source code must retain the above copyright notice, this
 *    list of conditions and the following disclaimer.
 *  * Redistributions in binary form must reproduce the above copyright notice, this
 *    list of conditions and the following disclaimer in the documentation and/or
 *    other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * PHP Version 5.3
 *
 * @category System
 * @package  Lagged_Munin
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  GIT: $Id$
 * @link     http://github.com/lagged/Lagged_Munin
 */
namespace Lagged\Munin;

/**
 * The CliRunner is a general purpose CLI.
 *
 * It'll respond to 'config', 'autoconf' and 'fetch' commands from munin-node.
 *
 * Required from any class implementing \Lagged\Munin\Plugin is implementing a
 * process() method so the runner can call the code to generate the datapoint
 * values.
 *
 * @author Till Klampaeckel <till@php.net>
 */
final class CliRunner
{
    public static function handle(Plugin $plugin)
    {
        if (isset($_SERVER['argv'])
            && is_array($_SERVER['argv'])
            && isset($_SERVER['argv'][1])
        ) {
            $argv = $_SERVER['argv'];
            if ($argv[1] == 'config') {
                echo $plugin;
                return 0;
            }
            if ($argv[1] == 'autoconf') {
                echo $plugin->getAutoConf() . "\n";
                return 0;
            }
            if ($argv[1] != 'fetch') {
                return 0;
            }
        }
        // process the actual data collection
        $plugin->process();

        // output values
        echo $plugin->getValues();
        return 0;
    }
}
