<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit\Surfaces;

use Jeremeamia\Slack\BlockKit\{Exception, HydrationData};

/**
 * App-published messages are dynamic yet transient spaces. They allow users to complete workflows among their
 * Slack conversations.
 *
 * @see https://api.slack.com/surfaces
 */
class Message extends Surface
{
    private const EPHEMERAL = ['response_type' => 'ephemeral'];
    private const IN_CHANNEL = ['response_type' => 'in_channel'];
    private const REPLACE_ORIGINAL = ['replace_original' => 'true'];
    private const DELETE_ORIGINAL = ['delete_original' => 'true'];

    private const VALID_DIRECTIVES = [
        self::EPHEMERAL,
        self::IN_CHANNEL,
        self::REPLACE_ORIGINAL,
        self::DELETE_ORIGINAL,
    ];

    /** @var array|string[] A message can have a directive (e.g., response_type) included along with its blocks. */
    private $directives = [];

    /** @var array|Attachment[] Attachments containing secondary content. */
    private $attachments = [];

    /**
     * Configures message to send privately to the user.
     *
     * This is default behavior for most interactions, and doesn't necessarily need to be explicitly configured.
     *
     * @return static
     */
    public function ephemeral(): self
    {
        return $this->directives(self::EPHEMERAL);
    }

    /**
     * Configures message to send to the entire channel.
     *
     * @return static
     */
    public function inChannel(): self
    {
        return $this->directives(self::IN_CHANNEL);
    }

    /**
     * Configures message to "replace_original" mode.
     *
     * @return static
     */
    public function replaceOriginal(): self
    {
        return $this->directives(self::REPLACE_ORIGINAL);
    }

    /**
     * Configures message to "delete_original" mode.
     *
     * @return static
     */
    public function deleteOriginal(): self
    {
        return $this->directives(self::DELETE_ORIGINAL);
    }

    /**
     * @param array $directives
     * @return static
     */
    public function directives(array $directives): self
    {
        $this->directives = $directives;

        return $this;
    }

    /**
     * @param Attachment $attachment
     * @return static
     */
    public function addAttachment(Attachment $attachment): self
    {
        $this->attachments[] = $attachment->setParent($this);

        return $this;
    }

    /**
     * @return Attachment
     */
    public function newAttachment(): Attachment
    {
        $attachment = new Attachment();
        $this->addAttachment($attachment);

        return $attachment;
    }

    public function validate(): void
    {
        if (!empty($this->directives) && !in_array($this->directives, self::VALID_DIRECTIVES, true)) {
            throw new Exception('Invalid directives for message');
        }

        $hasBlocks = !empty($this->getBlocks());
        if ($hasBlocks) {
            parent::validate();
        }

        if (!$hasBlocks && empty($this->attachments)) {
            throw new Exception('A message must contain blocks and/or attachments');
        }

        foreach ($this->attachments as $attachment) {
            $attachment->validate();
        }
    }

    public function toArray(): array
    {
        $data = $this->directives + parent::toArray();

        if ($this->attachments) {
            $data['attachments'] = [];
            foreach ($this->attachments as $attachment) {
                $data['attachments'][] = $attachment->toArray();
            }
        }

        if (empty($data['blocks'])) {
            unset($data['blocks']);
        }

        return $data;
    }

    protected function hydrate(HydrationData $data): void
    {
        $this->directives(array_filter([
            'response_type' => $data->useValue('response_type'),
            'replace_original' => $data->useValue('replace_original'),
            'delete_original' => $data->useValue('delete_original'),
        ]));

        foreach ($data->useElements('attachments') as $attachment) {
            $this->addAttachment(Attachment::fromArray($attachment));
        }

        parent::hydrate($data);
    }
}
