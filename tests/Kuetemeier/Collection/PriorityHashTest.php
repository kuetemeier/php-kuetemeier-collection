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

}
