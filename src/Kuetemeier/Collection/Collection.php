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
class Collection
{

    /**
     * Holds registered elements.
     *
     * @var array Holds registered elements.
     */
    protected $elements = array();

    public function __construct($initValues = null)
    {
        if (!isset($initValues)) {
            return;
        }

        if (is_array($initValues)) {
            $this->elements = $initValues;
        } elseif (is_string($initValues)) {
            $this->loadFromJSONFile($initValues);
        }
    }


    // NOTE: removed __get() to force syntax errors and for better performance.


    /**
     * Retrieve an element via its provided key.
     *
     * **HINT** Performance considerations - PHP uses copy on write, passing a value is ALWAYS faster, than passing
     * a reference by ourself (PHP needs overhead for that). So use `get` not `getRef` in
     * normal situations.
     *
     * **NOTE** We are not trimming keys.
     *
     * @param string $ukey    (optional) Unique element key. If none is given, return all elements.
     * @param mixed  $default Default value to return if element does not exist (key not found).
     *
     * @return mixed Stored element or $default value.
     *
     * @since 0.1.0
     * @see   Collection::getRef() Get a reference of an element.
     * @see   http://www.phpinternalsbook.com/zvals/memory_management.html Copy on Write in PHP
     */
    public function get($ukey = null, $default = null)
    {
        if (!isset($ukey)) {
            return $this->elements;
        }

        $element = $this->elements;

        foreach (explode('/', $ukey) as $key) {
            if (!isset($element[$key])) {
                return $default;
            }
            $element = $element[$key];
        }

        return $element;
    }


    /**
     * Retrieve a reference to an element via its provided key.
     *
     * **WARNING:** With a reference, you will directly change the representation of
     *          the data in this collection. But that was exactly what you was
     *          expecting, right? ;-)
     *
     * **IMPORTANT:** Remember to call it like this (with extra `&`):
     * ```
     * // init a collection with some values
     * $c = new Collection(self::TEST_ARRAY);
     *
     * // get a reference of an element
     * $e = &$c->getRef('some/key');
     * ```
     * Do you noticed the extra `&`, when we call the method?
     * If we would just use `$e = $c->getRef('some/key')` the equal operator
     * would create a COPY and not a REFERENCE.
     *
     * P.S.
     * There is no `$default` as in `Collection::get()`. If we cannot find the key,
     * we cannot pass a reference. It will return `null`.
     *
     * @param string $ukey    (optional) Unique element key. If none is given, return all elements.
     *
     * @return mixed Stored element or null if no element is found.
     *
     * @since 0.1.0
     * @see   Collection::get() Get a copy of an element.
     */
    public function &getRef($ukey = null)
    {
        if (!isset($ukey)) {
            return $this->elements;
        }

        $element = &$this->elements;

        foreach (explode('/', $ukey) as $key) {
            if (!isset($element[$key])) {
                return null;
            }
            $element = &$element[$key];
        }

        return $element;
    }


    // NOTE: removed __set() to force syntax errors.


    /**
     * Store an element into this collection, bound to a specified key.
     *
     * @param string $ukey  Unique element key.
     * @param mixed  $value The Element to store.
     *
     * @return bool True on successfull storage, false otherwise
     *
     * @since 0.1.0
     */
    public function set($ukey, $value, $override = false)
    {
        if ((!isset($ukey)) || (!is_string($ukey))) {
            return false;
        }

        $element = &$this->elements;
        foreach (explode('/', $ukey) as $key) {
            $element = &$element[$key];
        }
        if (isset($element) && (!$override)) {
            return false;
        }
        $element = $value;
        return true;
    }


    /**
     * Removes the element with the unique key `$ukey` from this collection.
     *
     * @param string $ukey  Unique element key.
     *
     * @return bool true if found and removed, false otherwise
     *
     * @since 0.1.0
     */
    public function unsetItem($ukey)
    {
        if ((!isset($ukey)) || (!is_string($ukey))) {
            return false;
        }

        $element = &$this->elements;
        $prev = &$element;

        $keys = explode('/', $ukey);
        $count = count($keys);
        $key = '';
        for ($i = 0; $i < $count; $i++) {
            $key = $keys[$i];
            if (isset($element[$key])) {
                $prev = &$element;
                $element = &$element[$key];
            } else {
                return false;
            }
        }
        unset($prev[$key]);
        return true;
    }


    /**
     * Check if an element with the unique key `$ukey` exists in this collection.
     *
     * @param string $ukey Unique element key.
     *
     * @return bool True if element existst, false otherwise.
     *
     * @since 0.1.0
     */
    public function has($ukey)
    {
        $element = $this->elements;
        foreach (explode('/', $ukey) as $key) {
            if (!isset($element[$key])) {
                return false;
            }
            $element = $element[$key];
        }
        return true;
    }


    /**
     * Returns the count of the FIRST level of keys.
     *
     * @return int Count of elements on the first key level.
     *
     * @since 0.1.0
     */
    public function count()
    {
        return count($this->elements);
    }


    /**
     * Tests if there are no elements in this collection.
     *
     * @return bool true if empty, false if not..
     *
     * @since 0.1.0
     */
    public function isEmpty()
    {
        return $this->count() === 0;
    }


    /**
     * Returns the elements in this collection as a JSON-encoded string.
     *
     * @return string A JSON representation of this Collection.
     *
     * @since 0.1.0
     * @see   http://php.net/manual/de/language.oop5.magic.php#object.tostring Magic __toString()
     */
    public function __toString()
    {
        return json_encode($this->elements);
    }


    /**
     * Clear up all elements.
     *
     * @return Collection $this - for possible chaining.
     *
     * @since 0.1.0
     */
    public function clear()
    {
        $this->elements = array();

        return $this;
    }


    // NOTE: a real merge cannot be implementet, because we don't know anything
    // about the type or content of the given elements. This may be done by a
    // subclass only allowing a specific Object-Type (e.g. with an Interface) of elements.


    /**
     * Merge the elements of another Collection into this Collection.
     *
     * If elements have the same unique key on the FIRST level,
     * they will get replaced with elements of the given Collection `$collection`.
     * Therefore, all elements on the SECOND level will also be removed, if
     * there is a duplicate key on the FIRST level.
     *
     * WARNING: Ensure, that all unique keys are strings!
     *
     * @param Collection $collection A valid instance of Collection.
     *
     * @return Collection $this - for possible chaining.
     *
     * @since 0.1.0
     */
    public function fastMerge(Collection $collection)
    {
        $other_elements = $collection->get();

        if (isset($this->elements) && isset($other_elements)) {
            $this->elements = array_merge($this->elements, $other_elements);
        }

        return $this;
    }


    /**
     * Split a subpart of elements identified by `$ukey` into a seperate Collection object.
     *
     * @param string $ukey Unique element key.
     *
     * @return Collection A new Collection object, initialized with found elements for `$ukey`.
     *
     * @since 0.1.0
     */
    public function split($ukey)
    {
        return new static($this->get($ukey));
    }


    /**
     * Load elements from a JSON string.
     *
     * @param string $json_string A string containing valid JSON code.
     * @param bool $overwrite true: clear and fill elements from JSON, false: do a fastMerge()
     *
     * @return bool true if successfull, false otherwise.
     * @see    Collection::fastMerge()
     */
    public function loadFromJSON($json_string, $overwrite = true)
    {
        $json = json_decode($json_string, true);

        if (!isset($json)) {
            return false;
        }

        if ($overwrite) {
            $this->elements = $json;
        } else {
            if (empty($this->fastMerge(new Collection($json)))) {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }


    /**
     * Load elements from a JSON file.
     *
     * @param string $filename Path to a valid JSON file.
     * @param bool   $override (optional) true: Clear and Overwrite, false: fastMerge. Default: true.
     *
     * @return bool true if successfull, false otherwise.
     *
     * @since 0.1.0
     */
    public function loadFromJSONFile($filename, $overwrite = true)
    {
        $json = file_get_contents($filename);

        if (!isset($json)) {
            return false;
        }

        return $this->loadFromJSON($json, $overwrite);
    }


    public function map(callable $callback)
    {
        $this->elements = array_map($callback, $this->elements);
    }
}
