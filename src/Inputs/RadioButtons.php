<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit\Inputs;

use Jeremeamia\Slack\BlockKit\Exception;
use Jeremeamia\Slack\BlockKit\Partials\Option;

class RadioButtons extends InputElement
{

    use HasConfirm;

    private const MIN_OPTIONS = 1;
    private const MAX_OPTIONS = 10;

    /** @var array|Option[] */
    private $options = [];

    /** @var Option|null */
    private $initialOption = null;

    public function addOption(Option $option, bool $isInitial = false): RadioButtons {

        $this->options[] = $option;

        if ($isInitial) {
            $this->initialOption = $option;
        }

        return $this;

    }

    public function validate(): void
    {

        if (count($this->options) > self::MAX_OPTIONS) {
            throw new Exception('Option Size cannot exceed %d', [self::MAX_OPTIONS]);
        }

        if (count($this->options) < self::MIN_OPTIONS) {
            throw new Exception('Option Size must be at least %d', [self::MIN_OPTIONS]);
        }

    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if (! empty($this->initialOption)) {
            $data['initial_option'] = $this->initialOption;
        }

        $data['options'] = $this->options;

        if (!empty($this->confirm)) {
            $data['confirm'] = $this->confirm->toArray();
        }

        return $data;

    }
}
