<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit\Inputs\SelectMenus;

class ChannelSelectMenu extends SelectMenu
{
    /** @var string */
    private $initialChannel;

    /** @var bool */
    private $responseUrlEnabled;

    /**
     * @param string $initialChannel
     * @return static
     */
    public function initialChannel(string $initialChannel): self
    {
        $this->initialChannel = $initialChannel;

        return $this;
    }

    /**
     * @param bool $enabled
     * @return static
     */
    public function responseUrlEnabled(bool $enabled): self
    {
        $this->responseUrlEnabled = $enabled;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if (!empty($this->initialChannel)) {
            $data['initial_channel'] = $this->initialChannel;
        }

        if (!empty($this->responseUrlEnabled)) {
            $data['response_url_enabled'] = $this->responseUrlEnabled;
        }

        return $data;
    }
}
