<?php

namespace AutoMapperPlus\PropertyAccessor;

use AutoMapperPlus\Test\Models\Inheritance\SourceChild;
use AutoMapperPlus\Test\Models\Issues\Issue33\User;
use AutoMapperPlus\Test\Models\Typed\TypedDestination;
use AutoMapperPlus\Test\Models\Visibility\InheritedVisibility;
use PHPUnit\Framework\TestCase;
use AutoMapperPlus\Test\Models\Visibility\Visibility;

class PropertyAccessorTest extends TestCase
{
    public function testItGetsAPublicProperty(): void
    {
        $accessor = new PropertyAccessor();
        $visibility = new Visibility();

        $this->assertTrue($accessor->getProperty($visibility, 'publicProperty'));
    }

    /**
     * @group stdClass
     */
    public function testItGetsAPublicPropertyForAStdClass(): void
    {
        $accessor = new PropertyAccessor();
        $obj = new \stdClass();
        $obj->name = 'stdClassName';

        $this->assertEquals('stdClassName', $accessor->getProperty($obj, 'name'));
    }

    public function testItGetsAProtectedProperty(): void
    {
        $accessor = new PropertyAccessor();
        $visibility = new Visibility();

        $this->assertTrue($accessor->getProperty($visibility, 'protectedProperty'));
    }

    public function testItGetsAPrivateProperty(): void
    {
        $accessor = new PropertyAccessor();
        $visibility = new Visibility();

        $this->assertTrue($accessor->getProperty($visibility, 'privateProperty'));
    }

    public function testItSetsAPublicProperty(): void
    {
        $accessor = new PropertyAccessor();
        $visibility = new Visibility();
        $accessor->setProperty($visibility, 'publicProperty', false);

        $this->assertFalse($visibility->getPublicProperty());
    }

    /**
     * @group stdClass
     */
    public function testItSetsAPublicPropertyForAStdClass(): void
    {
        $accessor = new PropertyAccessor();
        $obj = new \stdClass();
        $obj->name = 'stdClassName';
        $accessor->setProperty($obj, 'name', 'overridden');

        $this->assertEquals('overridden', $obj->name);
    }

    public function testItSetsAProtectedProperty(): void
    {
        $accessor = new PropertyAccessor();
        $visibility = new Visibility();
        $accessor->setProperty($visibility, 'protectedProperty', false);

        $this->assertFalse($visibility->getProtectedProperty());
    }

    public function testItSetsAPrivateProperty(): void
    {
        $accessor = new PropertyAccessor();
        $visibility = new Visibility();
        $accessor->setProperty($visibility, 'privateProperty', false);

        $this->assertFalse($visibility->getPrivateProperty());
    }

    public function testItCanCheckIfAPropertyExists(): void
    {
        $accessor = new PropertyAccessor();
        $visibility = new Visibility();

        $this->assertTrue($accessor->hasProperty($visibility, 'publicProperty'));
        $this->assertTrue($accessor->hasProperty($visibility, 'protectedProperty'));
        $this->assertTrue($accessor->hasProperty($visibility, 'privateProperty'));
        $this->assertFalse($accessor->hasProperty($visibility, 'noProperty'));
    }

    /**
     * @group stdClass
     */
    public function testItCanCheckIfAPropertyExistsForAStdClass(): void
    {
        $accessor = new PropertyAccessor();
        $obj = new \stdClass();
        $obj->name = 'Name';

        $this->assertTrue($accessor->hasProperty($obj, 'name'));
        $this->assertFalse($accessor->hasProperty($obj, 'no_name'));
    }

    /**
     * Test if it can fetch properties defined on the parent class.
     */
    public function testItHandlesInheritance(): void
    {
        $accessor = new PropertyAccessor();
        $source = new SourceChild('MyName');

        $this->assertTrue($accessor->hasProperty($source, 'name'));
        $this->assertEquals('MyName', $accessor->getProperty($source, 'name'));
    }

    public function testItWritesToAParentsPrivateProperty(): void
    {
        $accessor = new PropertyAccessor();
        $source = new InheritedVisibility();

        $accessor->setProperty($source, 'privateProperty', 'new value');

        $this->assertEquals('new value', $source->getPrivateProperty());
    }

    public function testItWritesToAParentsProtectedProperty(): void
    {
        $accessor = new PropertyAccessor();
        $source = new InheritedVisibility();

        $accessor->setProperty($source, 'protectedProperty', 'new value');

        $this->assertEquals('new value', $source->getProtectedProperty());
    }

    public function testItWritesToAParentsPublicProperty(): void
    {
        $accessor = new PropertyAccessor();
        $source = new InheritedVisibility();

        $accessor->setProperty($source, 'publicProperty', 'new value');

        $this->assertEquals('new value', $source->getPublicProperty());
    }

    /**
     * @see https://github.com/mark-gerarts/automapper-plus/issues/33
     */
    public function testItWritesCorrectlyWhenPropertiesShareASuffix(): void
    {
        $accessor = new PropertyAccessor();
        $source = new User();

        $accessor->setProperty($source, 'phone', 'phone-value');

        $this->assertEquals('phone-value', $source->getPhone());
    }

    /**
     * @requires PHP >= 7.4
     */
    public function testItGetsTypedPropertyNames(): void
    {
        $accessor = new PropertyAccessor();
        $destination = new TypedDestination();

        $properties = $accessor->getPropertyNames($destination);

        $this->assertEquals(['name'], $properties);
    }
}
