<?php
declare(strict_types=1);

namespace Kuetemeier\Collection;

use PHPUnit\Framework\Error\Warning;
use PHPUnit\Framework\TestCase;

final class PriorityHashTest extends TestCase
{

    const TEST_ARRAY = array(
        'one' => '1',
        'two' => '2',
        'three' => array(
            'A-one' => '31',
            'B-two' => '32',
            'C-three' => array (
                'string' => 'A string',
                'int' => 10,
                'bool' => false,
                'null' => null
            )
        )

    );


    public function testCanBeCreated(): void
    {
        $p = new PriorityHash();
        $this->assertInstanceOf(
            PriorityHash::class,
            $p
        );

        $this->assertEquals(0, $p->count());
        $this->assertEquals(true, $p->is_empty());
    }

    public function initPriorityHash(): PriorityHash
    {
        $p = new PriorityHash();

        $p->set("a", 10, "test1");
        $p->set("b", 20, "test2");
        $p->set("c", 5, "test3");

        return $p;
    }

    public function testSet(): void
    {
        $p = $this->initPriorityHash();

        $this->assertEquals(3, $p->count());
        $this->assertEquals(array('c', 'a', 'b'), $p->keys());
    }

    public function testUnSet(): void
    {
        $p = $this->initPriorityHash();

        $p->unset('b');

        $this->assertEquals(2, $p->count());
        $this->assertEquals(array('c', 'a'), $p->keys());
    }

    public function testMap(): void
    {
        $p = $this->initPriorityHash();

        $p->map(function($value) { return $value.'-map'; });

        $this->assertEquals(3, $p->count());
        $this->assertEquals(array('c', 'a', 'b'), $p->keys());
        $this->assertEquals(array('test3-map', 'test1-map', 'test2-map'), $p->values());
    }

    public function testGetArray(): void
    {
        $p = $this->initPriorityHash();

        //$this->assertEquals()
    }


}
