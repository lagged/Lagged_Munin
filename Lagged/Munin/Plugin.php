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
 * @author Till Klampaeckel <till@php.net>
 */
abstract class Plugin
{
    /**
     * @var string $autoConf For when munin calls ./plugin autoconf
     */
    protected $autoConf = 'no';

    /**
     * @var array $graph Properties of the graph.
     * @see self::__construct(), self::__set(), self::__toString()
     */
    protected $graph = array(
        'title'    => null,
        'category' => null,
        'vlabel'   => null,
        'period'   => null,
        'scale'    => null,
    );

    /**
     * @var array $data The actual data by datapoint.
     * @see self::$dataPoints, self::setValue()
     */
    protected $data;

    /**
     * @var array $dataPoints The definition for the data points of the graph.
     * @see self::setUpDataPoints()
     */
    protected $dataPoints = array();

    /**
     * __construct()
     *
     * @param string $title    The graph's title.
     * @param string $category The graph category (for display)
     * @param string $vlabel   The vertical label on the graph.
     * @param string $period   Either minute, or second (default).
     * @param string $scale    Either yes (default), or no
     *
     * @return $this
     * @uses   self::__set()
     */
    public function __construct($title = 'title', $category = 'other', $vlabel = 'label',
        $period = 'second', $scale = 'yes'
    ) {
        $this->title    = $title;
        $this->category = $category;
        $this->vlabel   = $vlabel;
        $this->period   = $period;
        $this->scale    = $scale;
    }

    /**
     * Magically! Set graph properties and do some basic validation!
     *
     * @param string $var
     * @param string $value
     *
     * @return void
     * @throws \OutOfBoundsException     On unknown property.
     * @throws \UnexpectedValueException In case validation fails.
     * @uses   self::$graph
     */
    public function __set($var, $value)
    {
        if (!array_key_exists($var, $this->graph)) {
            throw new \OutOfBoundsException("Unknown graph property: $var");
        }
        switch ($var) {
        case 'title':
        case 'category':
        case 'vlabel':
            break;
        case 'period':
            if ($value != 'minute' && $value != 'second') {
                throw new \UnexpectedValueException("Invalid value for graph_period: {$value}");
            }
            break;
        case 'scale':
            if ($value != 'yes' && $value != 'no') {
                throw new \UnexpectedValueException("Invalid value for graph_scale: {$value}");
            }
        }
        $this->graph[$var] = $value;
    }

    /**
     * Implement this in the plugin.
     */
    abstract protected function setUpDataPoints();

    public function getAutoConf()
    {
        return $this->autoConf;
    }

    public function getValues()
    {
        if (empty($this->data)) {
            throw new \LogicException("Set values first, before you ask for them.");
        }
        $response = '';
        foreach ($this->data as $point => $value) {
            $response .= "{$point}.value = {$value}\n";
        }
        return $response;
    }

    /**
     * For munin.
     *
     * @param string $value yes, or no
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setAutoConf($value)
    {
        if (!is_bool($value) && $value != 'no' && $value != 'yes') {
            throw new \InvalidArgumentException("Only yes or no are supported.");
        }
        if (!is_bool($value)) {
            $this->autoConf = $value;
            return $this;
        }
        $this->autoConf = (($value === true)?'yes':'no');
        return $this;
    }

    /**
     * Set a data points value - this is when the data is collected using munin-node.
     *
     * @param string $point
     * @param mixed  $value
     *
     * @return $this
     * @throws \OutOfBoundsException When the $point is unknown.
     * @uses   self::setUpDataPoints()
     * @see    self::$dataPoints
     */
    public function setValue($point, $value)
    {
        $this->setUpDataPoints();
        if (!array_key_exists($point, $this->dataPoints)) {
            throw new \OutOfBoundsException("Unknown datapoint: {$point}");
        }
        $this->data[$point] = $value;

        return $this;
    }

    /**
     * On ./plugin config this is used. It displays the configuration for the graph.
     *
     * @return string
     */
    public function __toString()
    {
        $this->setUpDataPoints();

        $response = '';
        foreach ($this->graph as $key => $value) {
            $response .= "{$key} {$value}\n";
        }
        foreach ($this->dataPoints as $point) {
            $response .= (string) $point;
        }
        return $response;
    }
}
