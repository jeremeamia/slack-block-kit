<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit\Inputs;

use Jeremeamia\Slack\BlockKit\Partials\{HasOptions, OptionsConfig};

class RadioButtons extends InputElement
{
    use HasConfirm;
    use HasOptions;

    protected function getOptionsConfig(): OptionsConfig
    {
        return OptionsConfig::new()
            ->setMinOptions(1)
            ->setMaxOptions(10)
            ->setMaxInitialOptions(1);
    }

    public function validate(): void
    {
        $this->validateOptions();
        $this->validateInitialOptions();

        if (!empty($this->confirm)) {
            $this->confirm->validate();
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = parent::toArray() + $this->getOptionsAsArray() + $this->getInitialOptionsAsArray();

        if (!empty($this->confirm)) {
            $data['confirm'] = $this->confirm->toArray();
        }

        return $data;
    }
}
