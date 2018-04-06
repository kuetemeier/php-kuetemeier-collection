<?php
declare(strict_types=1);

namespace Kuetemeier\Collection;

use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(
            Collection::class,
            new Collection()
        );
    }

    public function testOutput(): void
    {
        $this->expectOutputString('Hallo Welt - Hello World!');
        $collection = new Collection();
        $collection->helloWorld();
    }
/*
    public function testCannotBeCreatedFromInvalidEmailAddress(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Email::fromString('invalid');
    }

    public function testCanBeUsedAsString(): void
    {
        $this->assertEquals(
            'user@example.com',
            Email::fromString('user@example.com')
        );
    }
*/
}
