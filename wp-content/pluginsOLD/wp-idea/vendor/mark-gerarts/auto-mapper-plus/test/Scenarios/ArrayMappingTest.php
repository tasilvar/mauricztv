<?php

namespace AutoMapperPlus\Test\Scenarios;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\DataType;
use AutoMapperPlus\MappingOperation\Operation;
use AutoMapperPlus\Test\Models\Inheritance\SourceChild;
use AutoMapperPlus\Test\Models\Inheritance\SourceParent;
use AutoMapperPlus\Test\Models\Nested\ChildClass;
use AutoMapperPlus\Test\Models\Nested\ChildClassDto;
use AutoMapperPlus\Test\Models\Nested\ParentClass;
use AutoMapperPlus\Test\Models\SimpleProperties\Destination;
use PHPUnit\Framework\TestCase;

class ArrayMappingTest extends TestCase
{
    public function testItPerformsASimpleMapppingFromArray(): void
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(DataType::ARRAY, Destination::class);
        $mapper = new AutoMapper($config);

        $source = ['name' => 'John Doe'];
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('John Doe', $result->name);
    }

    public function testItPerformsAFromPropertyOperation(): void
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(DataType::ARRAY, Destination::class)
            ->forMember('name', Operation::fromProperty('full_name'));
        $mapper = new AutoMapper($config);

        $source = ['full_name' => 'John Doe'];
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('John Doe', $result->name);
    }

    public function testItPerformsAIgnoreOperation(): void
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(DataType::ARRAY, Destination::class)
            ->forMember('name', Operation::ignore());
        $mapper = new AutoMapper($config);

        $source = ['name' => 'John Doe'];
        $result = $mapper->map($source, Destination::class);

        $this->assertTrue(empty($result->name));
    }

    public function testItPerformsAMapFromOperation(): void
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(DataType::ARRAY, Destination::class)
            ->forMember('name', Operation::mapFrom(
                function ($source) {
                    $this->assertEquals(['name' => 'John Doe'], $source);
                    return 'Doe John';
                }));
        $mapper = new AutoMapper($config);

        $source = ['name' => 'John Doe'];
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('Doe John', $result->name);
    }

    public function testItPerformsAMapToOperation(): void
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(DataType::ARRAY, ParentClass::class)
            ->forMember('child', Operation::mapTo(ChildClass::class));
        $config->registerMapping(ChildClassDto::class, ChildClass::class);
        $mapper = new AutoMapper($config);

        $childDto = new ChildClassDto();
        $childDto->name = 'John Doe';
        $source = ['child' => $childDto];
        $result = $mapper->map($source, ParentClass::class);

        $this->assertInstanceOf(ChildClass::class, $result->child);
        $this->assertEquals('John Doe', $result->child->name);
    }

    public function testMapToHandlesAnArrayMapping(): void
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(DataType::ARRAY, ParentClass::class)
            ->forMember('child', Operation::mapTo(ChildClass::class, true));
        $config->registerMapping(DataType::ARRAY, ChildClass::class);
        $mapper = new AutoMapper($config);

        $childDto = ['name' => 'John Doe'];
        $source = ['child' => $childDto];
        $result = $mapper->map($source, ParentClass::class);

        $this->assertEquals('John Doe', $result->child->name);
    }

    public function testMapToDefaultsToAssumingACollection(): void
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(DataType::ARRAY, ParentClass::class)
            ->forMember('child', Operation::mapTo(ChildClass::class));
        $config->registerMapping(ChildClassDto::class, ChildClass::class);
        $mapper = new AutoMapper($config);

        $childDto = new ChildClassDto();
        $childDto->name = 'John Doe';
        $source = ['child' => [$childDto]];
        $result = $mapper->map($source, ParentClass::class);

        $this->assertIsArray($result->child);
        $this->assertEquals('John Doe', $result->child[0]->name);
    }

    public function testMapFromArrayWorksWithPolymorphism(): void
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(DataType::ARRAY, SourceParent::class);
        $mapper = new AutoMapper($config);

        $child = ['name' => 'child'];
        $result = $mapper->map($child, SourceChild::class);

        $this->assertEquals('child', $result->getName());
    }
}
