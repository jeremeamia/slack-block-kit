<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit\Inputs\SelectMenus;

use Jeremeamia\Slack\BlockKit\Partials\Option;

class MultiExternalSelectMenu extends MultiSelectMenu
{
    /** @var Option[] */
    private $initialOptions;

    /** @var int */
    private $minQueryLength;

    /**
     * @param array $options
     * @return self
     */
    public function initialOptions(array $options): self
    {
        foreach ($options as $name => $value) {
            $option = Option::new((string) $name, (string) $value);
            $option->setParent($this);
            $this->initialOptions[] = $option;
        }

        return $this;
    }

    public function minQueryLength(int $minQueryLength): self
    {
        $this->minQueryLength = $minQueryLength;

        return $this;
    }

    public function validate(): void
    {
        parent::validate();

        if (!empty($this->initialOptions)) {
            foreach ($this->initialOptions as $option) {
                $option->validate();
            }
        }
    }

    public function toArray(): array
    {
        $data = parent::toArray();

        if (!empty($this->initialOptions)) {
            $data['initial_options'] = array_map(function (Option $option) {
                return $option->toArray();
            }, $this->initialOptions);
        }

        if (isset($this->minQueryLength)) {
            $data['min_query_length'] = $this->minQueryLength;
        }

        return $data;
    }
}
