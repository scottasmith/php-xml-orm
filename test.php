<?php

use Ssmiff\XmlOrm\Attributes;
use Ssmiff\XmlOrm\Dehydrator\Dehydrator;
use Ssmiff\XmlOrm\Dehydrator\Exception\AttributeException;
use Ssmiff\XmlOrm\Dehydrator\Exception\ElementException;

require './vendor/autoload.php';

#[Attributes\Element()]
#[Attributes\Attribute('age', 10)]
class SimpleTest
{
    #[Attributes\Element('ConstTest1')]
    public const TEST_1 = '1';

    #[Attributes\ElementNS('http://www.w3.org/2005/Atom', 'ConstTest2')]
    public const TEST_2 = '2';

    #[Attributes\ElementNS('http://www.w3.org/2005/Atom2', 'ConstTest2:a')]
    public const TEST_3 = 3;
}

#[Attributes\AttributeElementNS('http://www.w3.org/2005/Atom2', 'ConstTest2:a')]
#[Attributes\Attribute('age', 10)]
class SimpleTest2
{
    #[Attributes\Attribute('ConstTest1')]
    public const TEST_1 = '1';

    #[Attributes\Attribute('ConstTest2')]
    public const TEST_2 = '2';

    #[Attributes\AttributeNS('http://www.w3.org/2005/Atom3', 'ConstTest4:b')]
    public const TEST_3 = 3;
}

#[Attributes\Element()]
class SimpleTest3
{
    #[Attributes\TextNode()]
    public const test1 = 'text1';

    #[Attributes\EntityReference()]
    public const test = 'copy';

    #[Attributes\TextNode()]
    public const test2 = 'text2';

    #[Attributes\Comment()]
    public const test3 = 'some comment';

    #[Attributes\CDATASection()]
    public const test4 = 'some cdata';
}

#[Attributes\ElementNS('http://example1.org', 'el')]
class ClassWithConstants
{
    public const const1 = 'test';

    #[Attributes\ElementNs('http://example2.org')]
    public const const4 = 123;

    #[Attributes\ElementNs('http://example3.org', 'tag5')]
    public const const5 = 'test2';

    #[Attributes\Element('tag1')]
    #[Attributes\Attribute('attr1', 'test')]
    public const const6 = 'val1';

    #[Attributes\AttributeNs('http://example4.org', 'r:attr1')]
    public const const7 = 'val2';

    #[Attributes\CDATASection('tag2')]
    public const const8 = 'cdata-eg';

    #[Attributes\TextNode()]
    public const const9 = 'start';

    #[Attributes\EntityReference()]
    public const const10 = 'copy';

    #[Attributes\TextNode()]
    public const const11 = 'end';

    #[Attributes\DocumentFragment()]
    public const const12 = '<copy/>';

    #[Attributes\Comment()]
    public const const13 = 'comment';
}

class ClassWithProperties
{
    public string $prop1 = 'test';

    #[Attributes\Element('tag1')]
    public int $prop4 = 123;

    #[Attributes\CDATASection('tag2')]
    public string $prop5 = 'test';

    #[Attributes\TextNode()]
    public string $prop6 = 'start';

    #[Attributes\EntityReference()]
    public string $prop7 = 'copy';

    #[Attributes\TextNode()]
    public string $prop8 = 'end';

    #[Attributes\DocumentFragment()]
    public string $prop9 = '<copy/>';

    #[Attributes\Comment()]
    public string $prop10 = 'comment';

    public function __construct(
        #[Attributes\Element()]
        public string $prop2,
    ) {
    }
}


class ClassWithMethods
{
    public function __construct(
        #[Attributes\Element()]
        public string $prop1
    )
    {
    }

   #[Attributes\ElementNS('http://example.com', 'tag1')]
    public function test1(): string
    {
        return 'abc';
    }

    #[Attributes\TextNode()]
    public function test2(): string
    {
        return 'start';
    }

    #[Attributes\EntityReference()]
    public function test3(): string
    {
        return 'copy';
    }

    #[Attributes\TextNode()]
    public function test4(): string
    {
        return 'end';
    }

    #[Attributes\ElementNS('http://example.com', 'tag2:e')]
    public function test5(): string
    {
        return 'abc';
    }
}

#[Attributes\ElementNS('http://example2.com', 'test2')]
class SimpleTest4
{

}

#[Attributes\ElementNS('http://example.com', 'e:test1')]
#[Attributes\AttributeNS('http://example2.com', 'e1:t', 'foo')]
#[Attributes\AttributeNS('http://example3.com', 'e2:t', 'bar')]
class MethodsReturningClasses
{
    #[Attributes\Attribute('foo', 'bar')]
    public function getSimpleElement(): SimpleTest4
    {
        return new SimpleTest4();
    }

    #[Attributes\ElementNs('http://example2.com', 'foo')]
    public function getSimpleElement2(): callable
    {
        return fn () => 'test';
    }
}

$simpleTest = new ClassWithMethods('test');

$doc = new DOMDocument('1.0', 'utf-8');
$doc->formatOutput = true;
$reflectionClass = new \ReflectionClass($simpleTest);

$classDehydrator = Dehydrator::createRoot($doc, $simpleTest, $reflectionClass);
try {
    $classDehydrator->dehydrate();
} catch (AttributeException|ElementException $e) {
    var_dump($e);
}

echo $doc->saveXML();
