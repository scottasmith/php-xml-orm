<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrmTest\Dehydrator;

use DOMDocument;
use ReflectionClass;
use Ssmiff\XmlOrm\Dehydrator\Dehydrator;
use Ssmiff\XmlOrmTest\Mocks;
use PHPUnit\Framework\TestCase;

class DehydrateTest extends TestCase
{
    /**
     * @dataProvider provideExpectedClassesAndXml
     */
    public function testCreatesXmlWithClasses(object $mockClass, string $expectedXml): void
    {
        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->formatOutput = true;
        $reflectionClass = new ReflectionClass($mockClass);

        $classDehydrator = Dehydrator::createRoot($doc, $mockClass, $reflectionClass);
        $classDehydrator->dehydrate();
        $this->assertSame(
            $expectedXml,
            trim($doc->saveXML())
        );
    }

    public function provideExpectedClassesAndXml(): array
    {
        return [
            'class-with-no-element' => [
                new Mocks\ClassWithNoElement(),
                '<?xml version="1.0" encoding="utf-8"?>
<ClassWithNoElement/>',
            ],
            'simple-empty-class' => [
                new Mocks\SimpleEmptyClass(),
                '<?xml version="1.0" encoding="utf-8"?>
<SimpleEmptyClass/>',
            ],
            'simple-empty-class-tagname' => [
                new Mocks\SimpleEmptyClassTagName(),
                '<?xml version="1.0" encoding="utf-8"?>
<tag1/>',
            ],
            'simple-ns-empty-class' => [
                new Mocks\SimpleNsEmptyClass(),
                '<?xml version="1.0" encoding="utf-8"?>
<SimpleNsEmptyClass xmlns="http://example3.com"/>',
            ],
            'simple-ns-empty-class-tagname' => [
                new Mocks\SimpleNsEmptyClassTagName(),
                '<?xml version="1.0" encoding="utf-8"?>
<tag1 xmlns="https://ns-test.com"/>',
            ],
            'empty-class-with-attributes' => [
                new Mocks\EmptyClassWithAttributes(),
                '<?xml version="1.0" encoding="utf-8"?>
<EmptyClassWithAttributes attr1="val1" attr2="val2"/>',
            ],
            'class-with-constants' => [
                new Mocks\ClassWithConstants(),
                "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" .
                '<el xmlns="http://example1.org">' .
                '<const1>test</const1>' .
                '<const2>VAL1</const2>' .
                '<const3>val1</const3>' .
                '<const4 xmlns="http://example2.org">123</const4>' .
                '<tag5 xmlns="http://example3.org">test2</tag5>' .
                '<tag1 attr1="test">val1</tag1>' .
                '<const7 xmlns:r="http://example4.org" r:attr1="attrVal">val2</const7>' .
                '<![CDATA[tag2]]>' .
                'start&copy;end' .
                '<copy/>' .
                '<!--comment-->' .
                '</el>',
            ],
            'class-with-properties' => [
                new Mocks\ClassWithProperties('test123', Mocks\TestEnum::VAL2),
                "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" .
                '<ClassWithProperties>' .
                '<prop1>test</prop1>'.
                '<prop3>val1</prop3>'.
                '<tag1>123</tag1>'.
                '<![CDATA[tag2]]>'.
                'start&copy;end'.
                '<copy/>'.
                '<!--comment-->'.
                '<!--comment2-->'.
                '<prop2>test123</prop2>'.
                '<prop11>VAL2</prop11>'.
                '</ClassWithProperties>'
            ],
            'class-with-methods' => [
                new Mocks\ClassWithMethods('test123'),
                "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" .
                '<ClassWithMethods>'.
                '<prop1>test123</prop1>'.
                '<tag1 xmlns="http://example.com">abc</tag1>'.
                'start&copy;end'.
                '<tag2:e xmlns:tag2="http://example.com">abc</tag2:e>'.
                '</ClassWithMethods>'
            ],
            'methods-returning-classes' => [
                new Mocks\MethodsReturningClasses(),
                "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" .
                '<e:test1'.
                ' xmlns:e="http://example.com"'.
                ' xmlns:e1="http://example2.com"'.
                ' xmlns:e2="http://example3.com"'.
                ' e1:t="foo"'.
                ' e2:t="bar">'.
                "\n".
                '  <e2:SimpleNsEmptyClass foo="foo"/>'.
                "\n".
                '  <e1:foo>test</e1:foo>'.
                "\n".
                '</e:test1>'
            ],
            'simple-with-formatter' => [
                new Mocks\SimpleWithFormatterClass(),
                "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" .
                "<SimpleWithFormatterClass>\n" .
                "  <DATE_TIME_TEST>2023-12-13T15:23:23+00:00</DATE_TIME_TEST>\n" .
                "  <dateTimeTest>2023-12-13T15:23:23+00:00</dateTimeTest>\n" .
                "  <testDateTime>13-12-2023</testDateTime>\n" .
                "</SimpleWithFormatterClass>"
            ]
        ];
    }
}
