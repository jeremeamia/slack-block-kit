<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit\Tests\Inputs;

use Jeremeamia\Slack\BlockKit\Exception;
use Jeremeamia\Slack\BlockKit\Inputs\Checkboxes;
use Jeremeamia\Slack\BlockKit\Partials\Confirm;
use Jeremeamia\Slack\BlockKit\Partials\Option;
use Jeremeamia\Slack\BlockKit\Tests\TestCase;
use Jeremeamia\Slack\BlockKit\Type;

/**
 * @covers \Jeremeamia\Slack\BlockKit\Inputs\Checkboxes
 */
class CheckBoxesTest extends TestCase
{
    public function testCheckboxesWithConfirm()
    {
        $input = (new Checkboxes('checkboxes-identifier'))
            ->addOption(new Option('foo', 'foo'))
            ->addOption(new Option('bar', 'bar'), true)
            ->addOption(new Option('foobar', 'foobar'), true)
            ->setConfirm(new Confirm('Switch', 'Do you really want to switch?', 'Yes switch'));

        $this->assertJsonData([
            'type' => Type::CHECKBOXES,
            'action_id' => 'checkboxes-identifier',
            'initial_options' => [
                [
                    'text' => [
                        'type' => 'plain_text',
                        'text' => 'bar',
                    ],
                    'value' => 'bar',
                ],
                [
                    'text' => [
                        'type' => 'plain_text',
                        'text' => 'foobar',
                    ],
                    'value' => 'foobar',
                ]
            ],
            'options' => [
                [
                    'text' => [
                        'type' => 'plain_text',
                        'text' => 'foo',
                    ],
                    'value' => 'foo',
                ],
                [
                    'text' => [
                        'type' => 'plain_text',
                        'text' => 'bar',
                    ],
                    'value' => 'bar',
                ],
                [
                    'text' => [
                        'type' => 'plain_text',
                        'text' => 'foobar',
                    ],
                    'value' => 'foobar',
                ],
            ],
            'confirm' => [
                'title' => [
                    'type' => 'plain_text',
                    'text' => 'Switch',
                ],
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => 'Do you really want to switch?',
                ],
                'confirm' => [
                    'type' => 'plain_text',
                    'text' => 'Yes switch',
                ],
                'deny' => [
                    'type' => 'plain_text',
                    'text' => 'Cancel',
                ],
            ]
        ], $input);
    }

    public function testTooManyOptions()
    {

        $this->expectException(Exception::class);
        $input = (new Checkboxes())
            ->addOption(new Option('foo', 'foo'))
            ->addOption(new Option('foo', 'foo'))
            ->addOption(new Option('foo', 'foo'))
            ->addOption(new Option('foo', 'foo'))
            ->addOption(new Option('foo', 'foo'))
            ->addOption(new Option('foo', 'foo'))
            ->addOption(new Option('foo', 'foo'))
            ->addOption(new Option('foo', 'foo'))
            ->addOption(new Option('foo', 'foo'))
            ->addOption(new Option('foo', 'foo'))
            ->addOption(new Option('foo', 'foo'));
        $input->validate();
    }

    public function testNoOptions()
    {
        $this->expectException(Exception::class);
        $input = new Checkboxes();
        $input->validate();
    }

}
