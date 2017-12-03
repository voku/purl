<?php

/*
 * This file is part of the Purl package, a project by Jonathan H. Wage.
 *
 * (c) 2013 Jonathan H. Wage
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Purl;

/**
 * AbstractPart class is implemented by each part of a Url where necessary.
 *
 * @author      Jonathan H. Wage <jonwage@gmail.com>
 * @implements  ArrayAccess
 */
abstract class AbstractPart implements \ArrayAccess
{
  /**
   * Flag for whether or not this part has been initialized.
   *
   * @var boolean
   */
  protected $initialized = false;

  /**
   * Array of data for this part.
   *
   * @var array
   */
  protected $data = [];

  /**
   * Array mapping part names to classes.
   *
   * @var array
   */
  protected $partClassMap = [];

  /**
   * Gets the data for this part. This method will initialize the part if it is not already initialized.
   *
   * @return array
   */
  public function getData(): array
  {
    $this->initialize();

    return $this->data;
  }

  /**
   * Sets the data for this part. This method will initialize the part if it is not already initialized.
   *
   * @param array $data
   *
   * @return $this
   */
  public function setData(array $data)
  {
    $this->initialize();
    $this->data = $data;

    foreach ($data as $key => $value) {
      if (empty($value) && $value !== '0') {
        unset($this->data[$key]);
      }
    }

    return $this;
  }

  /**
   * Check if this part has been initialized yet.
   *
   * @return boolean
   */
  public function isInitialized(): bool
  {
    return $this->initialized;
  }

  /**
   * Check if this part has data by key.
   *
   * @param string $key
   *
   * @return boolean
   */
  public function has($key): bool
  {
    $this->initialize();

    return isset($this->data[$key]);
  }

  /**
   * Gets data from this part by key.
   *
   * @param string $key
   *
   * @return mixed|null
   */
  public function get($key)
  {
    $this->initialize();

    return $this->data[$key] ?? null;
  }

  /**
   * Set data for this part by key.
   *
   * @param string $key
   * @param mixed  $value
   *
   * @return static
   */
  public function set($key, $value)
  {
    $this->initialize();
    $this->data[$key] = $value;

    return $this;
  }

  /**
   * Add data for this part.
   *
   * @param mixed $value
   *
   * @return $this
   */
  public function add($value)
  {
    $this->initialize();
    $this->data[] = $value;

    return $this;
  }

  /**
   * Remove data from this part by key.
   *
   * @param $key
   */
  public function remove($key)
  {
    $this->initialize();
    unset($this->data[$key]);
  }

  /** Property Overloading */

  /**
   * @param $key
   *
   * @return bool
   */
  public function __isset($key)
  {
    return $this->has($key);
  }

  /**
   * @param $key
   *
   * @return bool
   */
  public function __get($key)
  {
    return $this->get($key);
  }

  /**
   * @param $key
   * @param $value
   *
   * @return AbstractPart
   */
  public function __set($key, $value)
  {
    return $this->set($key, $value);
  }

  /**
   * @param $key
   */
  public function __unset($key)
  {
    return $this->remove($key);
  }

  /** ArrayAccess */

  /**
   * @param mixed $key
   *
   * @return bool
   */
  public function offsetExists($key): bool
  {
    $this->initialize();

    return isset($this->data[$key]);
  }

  /**
   * @param mixed $key
   *
   * @return bool
   */
  public function offsetGet($key): bool
  {
    return $this->get($key);
  }

  /**
   * @param mixed $key
   * @param mixed $value
   *
   * @return AbstractPart
   */
  public function offsetSet($key, $value): AbstractPart
  {
    return $this->set($key, $value);
  }

  /**
   * @param mixed $key
   */
  public function offsetUnset($key)
  {
    return $this->remove($key);
  }

  protected function initialize()
  {
    if ($this->initialized === true) {
      return;
    }

    $this->initialized = true;

    $this->doInitialize();
  }

  /**
   * Prepare a part value.
   *
   * @param string        $key
   * @param string|static $value
   *
   * @return static
   */
  protected function preparePartValue($key, &$value)
  {
    if (!isset($this->partClassMap[$key])) {
      return $value;
    }

    $className = $this->partClassMap[$key];

    return !$value instanceof $className ? new $className($value) : $value;
  }

  /**
   * Convert the instance back in to string form from the internal parts.
   *
   * @return string
   */
  abstract public function __toString();

  /**
   * Each part that extends AbstractPart must implement an doInitialize() method.
   *
   * @return void
   */
  abstract protected function doInitialize();
}
