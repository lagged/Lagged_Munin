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
 * Example:
 * <code>
 * class Bandwidth extends \Lagged\Munin\DataPoint {}
 * $bandwidth = new Bandwith();
 * $bandwidth->label = "Bandwidth Graph"
 * </code>
 *
 * @author Till Klampaeckel <till@php.net>
 */
abstract class DataPoint
{
    /**
     * @var array $data
     */
    protected $data = array(
        'label'  => null,
        'min'    => null,
        'draw'   => 'LINE1',
        'type'   => 'GAUGE',
        'colour' => null,
    );

    /**
     * @var string $name Name of the datapoint.
     */
    protected $name;

    /**
     * __construct()
     *
     * @return $this
     */
    public function __construct()
    {
        $this->name = strtolower(get_class($this));
    }

    /**
     * Get this datapoint's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * ZOMG Magic!!!11
     *
     * @param string $var   Property name.
     * @param string $value Value.
     *
     * @return $this
     * @throws \OutOfBoundsException
     * @todo   Add validation.
     */
    public function __set($var, $value)
    {
        if (!array_key_exists($var, $this->data)) {
            throw new \OutOfBoundsException("Invalid/unsupported property: {$var}");
        }
        $this->data[$var] = $value;
        return $this;
    }

    /**
     * Display the config when the plugin is asked: ./plugin config
     *
     * @see \Lagged\Munin\Plugin::__toString()
     */
    public function __toString()
    {
        $response = '';
        foreach ($this->data as $key => $value) {
            if ($value === null) {
                continue;
            }
            $response .= "{$this->name}.{$key} {$value}\n";
        }
        return $response;
    }
}
