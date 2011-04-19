<?php
namespace Lagged\Munin;

/**
 * @author Till Klampaeckel <till@php.net>
 */
abstract class Plugin
{
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
