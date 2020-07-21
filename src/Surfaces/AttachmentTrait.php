<?php


namespace Jeremeamia\Slack\BlockKit\Surfaces;


trait AttachmentTrait
{
    private $color;

    private $attachments;

    public function asAttach()
    {
        $this->attachments=['attachments' => [['color' => $this->color??'#ff000'] + parent::toArray()]];
        return $this->attachments;
    }

    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }
}
