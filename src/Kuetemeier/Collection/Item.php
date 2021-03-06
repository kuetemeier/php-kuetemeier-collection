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
abstract class Item
{
    protected $id;

    public function __construct($id = '')
    {
        $this->id = $id;
    }

    public function getID()
    {
        return $this->id;
    }

    public function setID($value)
    {
        $this->id = $value;
    }
}
