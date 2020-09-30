<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit\Surfaces;

use Jeremeamia\Slack\BlockKit\{Blocks\Input, Exception, Partials\PlainText, Type};

/**
 * Modals provide focused spaces ideal for requesting and collecting data from users, or temporarily displaying dynamic
 * and interactive information.
 *
 * @see https://api.slack.com/surfaces
 */
class Modal extends Surface
{
    private const MAX_LENGTH_TITLE = 24;

    /** @var PlainText */
    private $title;

    /** @var PlainText */
    private $submit;

    /** @var PlainText */
    private $close;

    /** @var string */
    private $privateMetadata;

    /** @var string */
    private $callbackId;

    /** @var string */
    private $externalId;

    /** @var bool */
    private $clearOnClose;

    /** @var bool */
    private $notifyOnClose;

    public function setTitle(PlainText $title): self
    {
        $this->title = $title->setParent($this);

        return $this;
    }

    public function setSubmit(PlainText $title): self
    {
        $this->submit = $title->setParent($this);

        return $this;
    }

    public function setClose(PlainText $title): self
    {
        $this->close = $title->setParent($this);

        return $this;
    }

    public function title(string $title): self
    {
        return $this->setTitle(new PlainText($title));
    }

    public function submit(string $submit): self
    {
        return $this->setSubmit(new PlainText($submit));
    }

    public function close(string $close): self
    {
        return $this->setClose(new PlainText($close));
    }

    public function externalId(string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function callbackId(string $callbackId): self
    {
        $this->callbackId = $callbackId;

        return $this;
    }

    public function privateMetadata(string $privateMetadata): self
    {
        $this->privateMetadata = $privateMetadata;

        return $this;
    }

    public function clearOnClose(bool $clearOnClose): self
    {
        $this->clearOnClose = $clearOnClose;

        return $this;
    }

    public function notifyOnClose(bool $notifyOnClose): self
    {
        $this->notifyOnClose = $notifyOnClose;

        return $this;
    }

    public function newInput(?string $blockId = null): Input
    {
        $block = new Input($blockId);
        $this->add($block);

        return $block;
    }

    public function validate(): void
    {
        parent::validate();

        if (empty($this->title)) {
            throw new Exception('Modals must have a "title"');
        }
        $this->title->validateWithLength(self::MAX_LENGTH_TITLE);

        $hasInputs = false;
        foreach ($this->getBlocks() as $block) {
            if ($block->getType() === Type::INPUT) {
                $hasInputs = true;
                break;
            }
        }
        if ($hasInputs && empty($this->submit)) {
            throw new Exception('Modals must have a "submit" button defined if they contain any "input" blocks');
        }
    }

    public function toArray(): array
    {
        $data = [];

        $data['title'] = $this->title->toArray();

        if (!empty($this->submit)) {
            $data['submit'] = $this->submit->toArray();
        }

        if (!empty($this->close)) {
            $data['close'] = $this->close->toArray();
        }

        if (!empty($this->externalId)) {
            $data['external_id'] = $this->externalId;
        }

        if (!empty($this->callbackId)) {
            $data['callback_id'] = $this->callbackId;
        }

        if (!empty($this->privateMetadata)) {
            $data['private_metadata'] = $this->privateMetadata;
        }

        if (!empty($this->clearOnClose)) {
            $data['clear_on_close'] = $this->clearOnClose;
        }

        if (!empty($this->notifyOnClose)) {
            $data['notify_on_close'] = $this->notifyOnClose;
        }

        $data += parent::toArray();

        return $data;
    }
}
