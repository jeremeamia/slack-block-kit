<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit\Partials;

use Jeremeamia\Slack\BlockKit\{Element, Exception};

class OptionGroup extends Element
{
    use HasOptions;

    /** @var PlainText */
    private $label;

    /**
     * @param string|null $label
     * @param array|null $options
     * @return OptionGroup
     */
    public static function new(?string $label = null, ?array $options = null): self
    {
        $optionGroup = new self();

        if ($label !== null) {
            $optionGroup->label($label);
        }

        if ($options !== null) {
            $optionGroup->options($options);
        }

        return $optionGroup;
    }

    protected function getOptionsConfig(): OptionsConfig
    {
        return OptionsConfig::new()->setMinOptions(1)->setMaxOptions(100);
    }

    /**
     * @param PlainText $label
     * @return self
     */
    public function setLabel(PlainText $label): self
    {
        $this->label = $label->setParent($this);

        return $this;
    }

    /**
     * @param string $label
     * @return static
     */
    public function label(string $label): self
    {
        return $this->setLabel(new PlainText($label, false));
    }

    public function validate(): void
    {
        if (empty($this->label)) {
            throw new Exception('OptionGroup element must contain a "label" element');
        }

        $this->label->validate();
        $this->validateOptions();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return parent::toArray()
            + ['label' => $this->label->toArray()]
            + $this->getOptionsAsArray();
    }
}
