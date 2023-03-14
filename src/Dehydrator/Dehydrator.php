<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrm\Dehydrator;

use BackedEnum;
use DOMDocument;
use DOMElement;
use DOMException;
use DOMNode;
use Generator;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use Ssmiff\XmlOrm\Attributes as A;
use Ssmiff\XmlOrm\Attributes\Formatters\Interfaces\FormatterInterface;
use Ssmiff\XmlOrm\Attributes\Interfaces\OptionalAttributeInterface;
use Ssmiff\XmlOrm\Attributes\Interfaces\UniqueAttributeInterface;
use Ssmiff\XmlOrm\Dehydrator\Exception\AttributeException;
use Ssmiff\XmlOrm\Dehydrator\Exception\ElementException;
use Ssmiff\XmlOrm\Dehydrator\Interfaces\DehydratorInterface;
use UnitEnum;

final readonly class Dehydrator implements DehydratorInterface
{
    private ?DOMNode $parentElement;

    private function __construct(
        private DOMDocument $doc,
        private object $class,
        private ReflectionClass $reflectionClass,
        private array $attributes = []
    ) {
    }

    public static function createRoot(
        DOMDocument $doc,
        object $class,
        ReflectionClass $reflectionClass
    ): self {
        $dehydrator = new self($doc, $class, $reflectionClass);
        $dehydrator->parentElement = $doc;
        return $dehydrator;
    }

    /**
     * @param DOMDocument $doc
     * @param object $class
     * @param ReflectionClass $reflectionClass
     * @param DOMNode $parentElement
     * @param array<ReflectionAttribute> $attributes
     * @return self
     */
    public static function createWithParent(
        DOMDocument $doc,
        object $class,
        ReflectionClass $reflectionClass,
        DOMNode $parentElement,
        array $attributes = [],
    ): self {
        $dehydrator = new self($doc, $class, $reflectionClass, $attributes);
        $dehydrator->parentElement = $parentElement;
        return $dehydrator;
    }

    /**
     * @throws AttributeException
     * @throws ElementException
     * @throws ReflectionException
     */
    public function dehydrate(): void
    {
        /**
         * @return array{DOMNode, ReflectionAttribute|UniqueAttributeInterface}
         * @throws AttributeException
         * @throws ElementException
         */
        $createElement = function (
            ReflectionClass|ReflectionClassConstant|ReflectionProperty|ReflectionMethod $reflectionSubComponent,
            string $tagName,
            ?string $value = null
        ): array {
            $attributes = $reflectionSubComponent->getAttributes();

            $uniqueAttribute = $this->getUniqueTagAttribute($attributes);

            $element = $this->createXmlElement($uniqueAttribute, $tagName, $value);
            $this->applyOptionalAttributes($element, $attributes);

            return [$element, $uniqueAttribute];
        };

        [$classElement, $uniqueAttribute] = $createElement(
            $this->reflectionClass,
            $this->reflectionClass->getShortName()
        );
        !empty($this->attributes) && $this->applyOptionalAttributes($classElement, $this->attributes);

        $this->parentElement->appendChild($classElement);

        foreach ($this->classComponents() as $reflectionSubComponent) {
            if ($uniqueAttribute instanceof A\AttributeElement
                || $uniqueAttribute instanceof A\AttributeElementNS
            ) {
                $attributes = $reflectionSubComponent->getAttributes();
                $this->applyOptionalAttributes($classElement, $attributes);
                continue;
            }

            $elementValue = $this->getElementValue(
                $this->class,
                $reflectionSubComponent
            );

            if (is_object($elementValue) && is_callable($elementValue)) {
                $elementValue = $elementValue();
            } elseif (
                is_object($elementValue)
                && !$elementValue instanceof UnitEnum
                && class_exists(get_class($elementValue))
            ) {
                self::createWithParent(
                    $this->doc,
                    $elementValue,
                    new ReflectionClass($elementValue),
                    $classElement,
                    $reflectionSubComponent->getAttributes()
                )->dehydrate();
                continue;
            }

            $attributes = $reflectionSubComponent->getAttributes();
            $elementValue = $this->formatValue($attributes, $elementValue);

            [$element] = $createElement($reflectionSubComponent, $reflectionSubComponent->getName(), $elementValue);

            $classElement->appendChild($element);
        }
    }

    /**
     * @return Generator<ReflectionClassConstant|ReflectionProperty|ReflectionMethod> $components
     */
    private function classComponents(): Generator
    {
        /** @var array<ReflectionClassConstant|ReflectionProperty|ReflectionMethod> $components */
        foreach ($this->reflectionClass->getReflectionConstants() as $reflectionConstant) {
            yield $reflectionConstant;
        };
        foreach ($this->reflectionClass->getProperties() as $reflectionProperty) {
            yield $reflectionProperty;
        };
        foreach ($this->reflectionClass->getMethods() as $reflectionMethod) {
            if ($reflectionMethod->isConstructor()
                || $reflectionMethod->isDestructor()
            ) {
                continue;
            }

            yield $reflectionMethod;
        };
    }

    /**
     * @param array<ReflectionAttribute> $attributes
     * @throws AttributeException
     */
    private function getUniqueTagAttribute(
        array $attributes
    ): ReflectionAttribute|UniqueAttributeInterface {
        $instances = array_filter(
            array_map(
                fn (ReflectionAttribute $attribute) => $attribute->newInstance(),
                $attributes
            ),
            fn ($attribute) => match (true) {
                $attribute instanceof A\AttributeElement,
                $attribute instanceof A\AttributeElementNs,
                $attribute instanceof A\Element,
                $attribute instanceof A\ElementNS,
                $attribute instanceof A\TextNode,
                $attribute instanceof A\CDATASection,
                $attribute instanceof A\EntityReference,
                $attribute instanceof A\DocumentFragment,
                $attribute instanceof A\Comment => true,
                default => false
            }
        );

        if (count($instances) > 1) {
            throw AttributeException::nonUnique($instances);
        } elseif (empty($instances)) {
            $instances[0] = new A\Element();
        }

        return $instances[0];
    }

    /**
     * @throws ElementException
     */
    private function createXmlElement(
        UniqueAttributeInterface $main,
        string $elementName,
        ?string $value = null
    ): DOMNode {
        $tagName = $main->tagName ?? $elementName;

        $createDocFragment = function (A\DocumentFragment $fragment) use ($value) {
            $instance = $this->doc->createDocumentFragment();
            $instance->appendXML($value ?? $fragment->xmlData);
            return $instance;
        };

        try {
            return match (true) {
                $main instanceof A\Element => $this->doc->createElement($tagName, $value ?? ''),
                $main instanceof A\ElementNS => $this->doc->createElementNS($main->ns, $tagName, $value ?? ''),
                $main instanceof A\CDATASection => $this->doc->createCDATASection($main->data ?? $value),
                $main instanceof A\Comment => $this->doc->createComment($main->comment ?? $value),
                $main instanceof A\DocumentFragment => $createDocFragment($main),
                $main instanceof A\EntityReference => $this->doc->createEntityReference($main->data ?? $value),
                $main instanceof A\TextNode => $this->doc->createTextNode($main->data ?? $value),
            };
        } catch (DOMException $exception) {
            throw ElementException::createElementFailed(
                get_class($main) . ":{$tagName}",
                $exception
            );
        }
    }

    /**
     * @param DOMElement $element
     * @param array<ReflectionAttribute> $attributes
     * @throws ElementException
     */
    private function applyOptionalAttributes(
        DOMNode $element,
        array $attributes
    ): void {
        $applyToElement = function (OptionalAttributeInterface $attribute) use ($element) {
            match (true) {
                $attribute instanceof A\Attribute => $element->setAttribute(
                    $attribute->tagName,
                    (string)$attribute->value
                ),
                $attribute instanceof A\AttributeNS => $element->setAttributeNS(
                    $attribute->namespace,
                    $attribute->tagName,
                    (string)$attribute->value
                )
            };
        };

        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();

            try {
                $instance instanceof OptionalAttributeInterface && $applyToElement($instance);
            } catch (DOMException $exception) {
                throw ElementException::createElementFailed(
                    get_class($instance) . ":{$instance->tagName}",
                    $exception
                );
            }
        }
    }

    /**
     * @throws ElementException
     */
    private function getElementValue(
        object $class,
        ReflectionClassConstant|ReflectionProperty|ReflectionMethod $reflection
    ): mixed {
        try {
            if ($reflection instanceof ReflectionMethod && $reflection->isStatic()) {
                return $reflection->invoke(null);
            } elseif ($reflection instanceof ReflectionMethod) {
                return $reflection->invoke($class);
            }
        } catch (ReflectionException $exception) {
            throw ElementException::createElementValueFailed($reflection->getName(), $exception);
        }

        if ($reflection instanceof ReflectionProperty && !$reflection->isStatic()) {
            return $reflection->getValue($class);
        }

        return $reflection->getValue();
    }

    /**
     * @param array<ReflectionAttribute> $attributes
     * @param mixed $value
     * @return string
     */
    private function formatValue(array $attributes, mixed $value): string
    {
        $instances = [
            // Default formatter
            new class () implements FormatterInterface {
                public function format(mixed $value): string
                {
                    if ($value instanceof BackedEnum) {
                        return (string)$value->value;
                    } elseif ($value instanceof UnitEnum) {
                        return (string)$value->name;
                    }

                    return (string)$value;
                }
            }
        ];

        foreach ($attributes as $attribute) {
            $instances[] = $attribute->newInstance();
        }

        foreach ($instances as $instance) {
            if ($instance instanceof FormatterInterface) {
                var_dump('test');
                $value = $instance->format($value);
            }
        }

        return $value;
    }
}
