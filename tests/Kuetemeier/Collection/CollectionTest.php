<?php
declare(strict_types=1);

namespace Kuetemeier\Collection;

use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
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
        $c = new Collection();

        $this->assertInstanceOf(
            Collection::class,
            $c
        );

        $this->assertEquals(0, $c->count());
    }


    public function testConstructWithArray(): void
    {
        $c = new Collection(self::TEST_ARRAY);

        // return first level element
        $this->assertEquals('1', $c->get('one'));
        // return second level element
        $this->assertEquals('31', $c->get('three/A-one'));
        // non existing first level key
        $this->assertEquals(null, $c->get('none'));
        // non existing secont level key
        $this->assertEquals(null, $c->get('not/in/array'));
        // no key
        $this->assertEquals(self::TEST_ARRAY, $c->get());

        $this->assertEquals(count(self::TEST_ARRAY), $c->count());
    }


    public function testElementsAreNotMutable(): void
    {
        $c = new Collection(self::TEST_ARRAY);

        $e = $c->get('three');

        $this->assertEquals('31', $e['A-one']);

        $e['A-one'] = 'Test';

        $this->assertEquals('Test', $e['A-one']);
        $this->assertEquals('31', $c->get('three/A-one'));

        $this->assertEquals(count(self::TEST_ARRAY), $c->count());
    }


    public function testRefElementsAreMutable(): void
    {

        // init with a const
        $c = new Collection(self::TEST_ARRAY);

        // get a reference of an element
        $e = &$c->getRef('three');

        // check if we get the right one
        $this->assertEquals('31', $e['A-one']);

        // manipultate element
        $e['A-one'] = 'Test';

        // get another reference to the complete elements array
        $n = &$c->getRef();

        // manipulate it
        $n['four'] = 'Test 4';
        $n['three']['B-two'] = 'Test B2';

        // tests
        $this->assertEquals('Test', $e['A-one']);
        $this->assertEquals('Test', $c->get('three/A-one'));
        // 'has' finds the new element
        $this->assertEquals(true, $c->has('four'));
        // and it has the right value
        $this->assertEquals('Test 4', $c->get('four'));
        $this->assertEquals('Test B2', $c->get('three/B-two'));

        $this->assertEquals(true, $c->has('four'));

        $this->assertEquals(count(self::TEST_ARRAY)+1, $c->count());
    }


    public function testHas(): void
    {
        // init with a const
        $c = new Collection();

        $this->assertEquals(false, $c->has('one'));


        // init with a const
        $c = new Collection(self::TEST_ARRAY);

        $this->assertEquals(true, $c->has('one'));
        $this->assertEquals(true, $c->has('three/B-two'));
    }


    public function testSet(): void
    {
        // test with an empty collection
        $c = new Collection();

        $this->assertEquals(false, $c->has('first'));

        $v = 'test';
        $c->set('first', $v);

        $this->assertEquals(true, $c->has('first'));
        $this->assertEquals($v, $c->get('first'));

        $v = array('1' => 'one', '2' => 'two');
        $c->set('second', $v);

        $this->assertEquals(true, $c->has('second'));
        $this->assertEquals($v, $c->get('second'));

        // test with an initialized collection
        $c = new Collection(self::TEST_ARRAY);

        $this->assertEquals(false, $c->has('first'));

        $v = 'test';
        $c->set('first', $v);

        $this->assertEquals(true, $c->has('first'));
        $this->assertEquals($v, $c->get('first'));

        $v = array('1' => 'one', '2' => 'two');
        $c->set('second', $v);

        $this->assertEquals(true, $c->has('second'));
        $this->assertEquals($v, $c->get('second'));
    }


    public function testToString(): void
    {
        $c = new Collection(self::TEST_ARRAY);

        $s = '' . $c;

        $this->assertEquals(json_encode(self::TEST_ARRAY), $s);
    }

    public function testClear(): void
    {
        $c = new Collection(self::TEST_ARRAY);

        $this->assertEquals(count(self::TEST_ARRAY), $c->count());

        $c->clear();

        $this->assertEquals(0, $c->count());
        $this->assertEquals(array(), $c->get());
    }

}
