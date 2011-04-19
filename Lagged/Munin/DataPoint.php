<?php
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
        'draw'   => null,
        'type'   => null,
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
            $response .= "{$this->name}.{$key} {$value}\n";
        }
        return $response;
    }
}
