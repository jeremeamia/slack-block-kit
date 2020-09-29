<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit\Blocks;

use Jeremeamia\Slack\BlockKit\{Element, Exception, Inputs, Type};

class Actions extends BlockElement
{
    private const MAX_ACTIONS = 5;

    /** @var Element[] */
    private $elements = [];

    /**
     * @param string|null $blockId
     * @param Element[] $elements
     */
    public function __construct(?string $blockId = null, array $elements = [])
    {
        parent::__construct($blockId);
        foreach ($elements as $element) {
            $this->add($element);
        }
    }

    public function add(Element $element): self
    {
        if (!in_array($element->getType(), Type::ACTION_ELEMENTS)) {
            throw new Exception('Invalid actions element type: %s', [$element->getType()]);
        }

        if (count($this->elements) >= self::MAX_ACTIONS) {
            throw new Exception('Context cannot have more than %d elements', [self::MAX_ACTIONS]);
        }

        $this->elements[] = $element->setParent($this);

        return $this;
    }

    public function newButton(?string $actionId = null): Inputs\Button
    {
        $action = new Inputs\Button($actionId);
        $this->add($action);

        return $action;
    }

    public function newDatePicker(?string $actionId = null): Inputs\DatePicker
    {
        $action = new Inputs\DatePicker($actionId);
        $this->add($action);

        return $action;
    }

    public function newSelectMenu(?string $actionId = null): Inputs\SelectMenus\SelectMenuFactory
    {
        return new Inputs\SelectMenus\SelectMenuFactory($actionId, function (Inputs\SelectMenus\SelectMenu $menu) {
            $this->add($menu);
        });
    }

    public function newMultiSelectMenu(?string $actionId = null): Inputs\SelectMenus\MultiSelectMenuFactory
    {
        return new Inputs\SelectMenus\MultiSelectMenuFactory($actionId, function (Inputs\SelectMenus\SelectMenu $menu) {
            $this->add($menu);
        });
    }

    public function newTextInput(?string $actionId = null): Inputs\TextInput
    {
        $action = new Inputs\TextInput($actionId);
        $this->add($action);

        return $action;
    }

    public function newRadioButtons(?string $actionId = null): Inputs\RadioButtons
    {
        $action = new Inputs\RadioButtons($actionId);
        $this->add($action);

        return $action;
    }

    public function newCheckboxes(?string $actionId = null): Inputs\Checkboxes
    {
        $action = new Inputs\Checkboxes($actionId);
        $this->add($action);

        return $action;
    }

    public function newOverflowMenu(?string $actionId = null): Inputs\OverflowMenu
    {
        $action = new Inputs\OverflowMenu($actionId);
        $this->add($action);

        return $action;
    }

    public function validate(): void
    {
        if (empty($this->elements)) {
            throw new Exception('Context must contain at least one element');
        }

        foreach ($this->elements as $element) {
            $element->validate();
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        $data['elements'] = [];
        foreach ($this->elements as $element) {
            $data['elements'][] = $element->toArray();
        }

        return $data;
    }
}
