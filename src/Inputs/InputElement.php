<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit\Inputs;

use Jeremeamia\Slack\BlockKit\Element;
use Jeremeamia\Slack\BlockKit\HydrationData;
use Jeremeamia\Slack\BlockKit\Partials\{Confirm, PlainText};

abstract class InputElement extends Element
{
    /** @var string */
    private $actionId;

    /**
     * @param string|null $actionId
     */
    public function __construct(?string $actionId = null)
    {
        if (!empty($actionId)) {
            $this->actionId($actionId);
        }
    }

    /**
     * @param string $actionId
     * @return static
     */
    public function actionId(string $actionId): self
    {
        $this->actionId = $actionId;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if (!empty($this->actionId)) {
            $data['action_id'] = $this->actionId;
        }

        return $data;
    }

    protected function hydrate(HydrationData $data): void
    {
        if ($data->has('action_id')) {
            $this->actionId($data->useValue('action_id'));
        }

        parent::hydrate($data);
    }
}
