<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit\Inputs\SelectMenus;

use Jeremeamia\Slack\BlockKit\HydrationData;

class MultiChannelSelectMenu extends MultiSelectMenu
{
    /** @var string[] */
    private $initialChannels;

    /**
     * @param string[] $initialChannels
     * @return static
     */
    public function initialChannels(array $initialChannels): self
    {
        $this->initialChannels = $initialChannels;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if (!empty($this->initialChannels)) {
            $data['initial_channels'] = $this->initialChannels;
        }

        return $data;
    }

    protected function hydrate(HydrationData $data): void
    {
        if ($data->has('initial_channels')) {
            $this->initialChannels($data->useArray('initial_channels'));
        }

        parent::hydrate($data);
    }
}
