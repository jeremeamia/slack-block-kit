<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit;

use JsonSerializable;

abstract class Element implements JsonSerializable
{
    /** @var Element|null */
    protected $parent;

    /** @var array */
    protected $extra;

    /**
     * @return static
     */
    public static function new()
    {
        return new static();
    }

    /**
     * @return Element|null
     */
    final public function getParent(): ?Element
    {
        return $this->parent;
    }

    /**
     * @param Element $parent
     * @return static
     */
    final public function setParent(Element $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return Type::mapClass(static::class);
    }

    /**
     * Allows setting arbitrary extra fields on an element.
     *
     * @param string $key
     * @param mixed $value
     * @return static
     */
    final public function setExtra(string $key, $value): self
    {
        $this->extra[$key] = $value;

        return $this;
    }

    /**
     * @param callable $tap
     * @return static
     */
    final public function tap(callable $tap): self
    {
        $tap($this);

        return $this;
    }

    /**
     * @throws Exception if the block kit item is invalid (e.g., missing data).
     */
    abstract public function validate(): void;

    /**
     * @return array
     */
    public function toArray(): array
    {
        $this->validate();
        $type = $this->getType();

        $data = !in_array($type, Type::HIDDEN_TYPES, true) ? compact('type') : [];

        foreach ($this->extra ?? [] as $key => $value) {
            $data[$key] = $value instanceof Element ? $value->toArray() : $value;
        }

        return $data;
    }

    /**
     * @return array
     */
    final public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param string $json
     * @return static
     */
    final public static function fromJson(string $json)
    {
        return static::fromArray(json_decode($json, true));
    }

    /**
     * @param array $data
     * @return static
     */
    final public static function fromArray(array $data)
    {
        $data = new HydrationData($data);

        // Determine element class to hydrate.
        // - If a type is present, map the type to the class.
        // - Type-mapped class must be the same as or a subclass of the late-static-bound class.
        // - If no type present, use the late-static-bound class.
        $class = static::class;
        if ($data->has('type')) {
            $typeClass = Type::mapType((string) $data->get('type'));
            if (is_a($typeClass, $class, true)) {
                $class = $typeClass;
            } else {
                throw new Exception('Element class mismatch in fromArray: %s is not a %s', [$typeClass, $class]);
            }
        }

        /** @var static $element */
        $element = new $class();
        $element->hydrate($data);

        return $element;
    }

    /**
     * @param HydrationData $data
     * @internal Used by fromArray implementations.
     */
    protected function hydrate(HydrationData $data): void
    {
        $type = $data->useValue('type');

        $class = get_class($this);
        if (is_string($type) && Type::mapType($type) !== $class) {
            throw new Exception('[Hydration] Type %s does not map to class %s.', [$type, $class]);
        }

        foreach ($data->getExtra() as $key => $value) {
            $this->setExtra($key, $value);
        }

        $this->validate();
    }
}
