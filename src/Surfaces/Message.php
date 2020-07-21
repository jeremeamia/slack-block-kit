<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit\Surfaces;

class Message extends Surface
{
    use AttachmentTrait;

    public const EPHEMERAL = 'ephemeral';
    public const IN_CHANNEL = 'in_channel';

    /** @var string */
    private $responseType = self::EPHEMERAL;

    /**
     * Configures whether the message is sent to the entire channel or not.
     *
     * @param bool $inChannel
     * @return Message
     */
    public function inChannel(bool $inChannel): self
    {
        $this->responseType = $inChannel ? self::IN_CHANNEL : self::EPHEMERAL;

        return $this;
    }

    public function toArray(): array
    {
        return ['response_type' => $this->responseType] + parent::toArray();
    }
}
