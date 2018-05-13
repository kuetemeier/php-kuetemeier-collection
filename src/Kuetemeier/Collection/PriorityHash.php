<?php
/**
 * Vim: set smartindent expandtab tabstop=4 shiftwidth=4 softtabstop=4:
 *
 * @author    Jörg Kütemeier (https://kuetemeier.de/kontakt)
 * @license   Apache-2.0
 * @link      https://kuetemeier.de
 * @copyright 2018 Jörg Kütemeier
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.
 */

namespace Kuetemeier\Collection;

/**
 * A common Collection PHP class to use as a baseline in your projects.
 *
 * This Collection acts like a Hash or Map. It stores its elements based
 * on a `string` key. The key may have multiple levels divided by a slash `/`
 * in the string.
 *
 * @author Jörg Kütemeier
 * @since  0.1.0
 */
class PriorityHash implements CollectionInterface
{
    protected $priorities = array();

    /**
     * Sorted Keys
     *
     * @see PriorityHash::buildSortedKeys()
     */
    protected $sortedKeys = array();

    protected $elements = array();

    public function __construct()
    {
    }

    public function count()
    {
        return count($this->elements);
    }

    public function isEmpty()
    {
        return $this->count() === 0;
    }

    protected function buildSortedKeys()
    {
        $keys = array_keys($this->priorities);
        usort($keys, function ($a, $b) {
            return (int) $this->priorities[$a] - (int) $this->priorities[$b];
        });
        $this->sortedKeys = $keys;
    }

    public function set($key, $priority, $value)
    {
        $this->elements[$key] = $value;
        $this->priorities[$key] = $priority;
        $this->buildSortedKeys();
    }

    public function get($key = null, $default = null)
    {
        if (!isset($key)) {
            $ret = array();
            foreach ($this->sortedKeys as $key) {
                array_push($ret, $this->elements[$key]);
            }
        } else {
            $ret = (isset($this->elements[$key])) ? $this->elements[$key] : $default;
        }
        return $ret;
    }

    public function has($key)
    {
        return isset($this->elements[$key]);
    }

    public function clear()
    {
        $priorities = array();
        $sortedKeys = array();
        $elements = array();
    }

    public function map($callback)
    {
        foreach ($this->sortedKeys as $key) {
            $this->elements[$key] = $callback($this->elements[$key]);
        }
    }

    public function doForeach($callback)
    {
        foreach ($this->sortedKeys as $key) {
            $callback($key, $this->elements[$key]);
        }
    }

    public function foreachWithArgs($callback, $args)
    {
        foreach ($this->sortedKeys as $key) {
            $callback($key, $this->elements[$key], $args);
        }
    }

    public function unset($key)
    {
        unset($this->elements[$key]);
        unset($this->priorities[$key]);
        $this->buildSortedKeys();
    }

    public function keys()
    {
        return $this->sortedKeys;
    }

    public function values()
    {
        // TODO: test if there is a faster way, e.g. with array_values and perhaps sort.
        $v = array();
        foreach ($this->sortedKeys as $key) {
            array_push($v, $this->elements[$key]);
        }
        return $v;
    }
}
