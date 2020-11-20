<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit\Tests\Surfaces;

use Jeremeamia\Slack\BlockKit\Exception;
use Jeremeamia\Slack\BlockKit\Surfaces\Message;
use Jeremeamia\Slack\BlockKit\Tests\TestCase;
use Jeremeamia\Slack\BlockKit\Type;

/**
 * @covers \Jeremeamia\Slack\BlockKit\Surfaces\Message
 */
class MessageTest extends TestCase
{
    public function testCanApplyEphemeralDirectives()
    {
        $data = Message::new()->ephemeral()->text('foo')->toArray();
        $this->assertArrayHasKey('blocks', $data);
        $this->assertArrayHasKey('response_type', $data);
        $this->assertEquals('ephemeral', $data['response_type']);
    }

    public function testCanApplyInChannelDirectives()
    {
        $data = Message::new()->inChannel()->text('foo')->toArray();
        $this->assertArrayHasKey('blocks', $data);
        $this->assertArrayHasKey('response_type', $data);
        $this->assertEquals('in_channel', $data['response_type']);
    }

    public function testCanApplyReplaceOriginalDirectives()
    {
        $data = Message::new()->replaceOriginal()->text('foo')->toArray();
        $this->assertArrayHasKey('blocks', $data);
        $this->assertArrayHasKey('replace_original', $data);
        $this->assertEquals('true', $data['replace_original']);
    }

    public function testCanApplyDeleteOriginalDirectives()
    {
        $data = Message::new()->deleteOriginal()->text('foo')->toArray();
        $this->assertArrayHasKey('blocks', $data);
        $this->assertArrayHasKey('delete_original', $data);
        $this->assertEquals('true', $data['delete_original']);
    }

    public function testDoesNotApplyDirectivesWhenNotSet()
    {
        $data = Message::new()->text('foo')->toArray();
        $this->assertArrayHasKey('blocks', $data);
        $this->assertArrayNotHasKey('response_type', $data);
        $this->assertArrayNotHasKey('replace_original', $data);
        $this->assertArrayNotHasKey('delete_original', $data);
    }

    public function testCanAddAttachments()
    {
        $msg = Message::new()->tap(function (Message $msg) {
            $msg->text('foo');
            $msg->newAttachment()->text('bar');
            $msg->newAttachment()->text('baz');
        });

        $this->assertJsonData([
            'blocks' => [
                [
                    'type' => Type::SECTION,
                    'text' => [
                        'type' => Type::MRKDWNTEXT,
                        'text' => 'foo',
                    ],
                ],
            ],
            'attachments' => [
                [
                    'blocks' => [
                        [
                            'type' => Type::SECTION,
                            'text' => [
                                'type' => Type::MRKDWNTEXT,
                                'text' => 'bar',
                            ],
                        ],
                    ],
                ],
                [
                    'blocks' => [
                        [
                            'type' => Type::SECTION,
                            'text' => [
                                'type' => Type::MRKDWNTEXT,
                                'text' => 'baz',
                            ],
                        ],
                    ],
                ],
            ]
        ], $msg);
    }

    public function testCanAddAttachmentsWithoutPrimaryBlocks()
    {
        $msg = Message::new()->tap(function (Message $msg) {
            $msg->newAttachment()->text('foo');
        });

        $this->assertJsonData([
            'attachments' => [
                [
                    'blocks' => [
                        [
                            'type' => Type::SECTION,
                            'text' => [
                                'type' => Type::MRKDWNTEXT,
                                'text' => 'foo',
                            ],
                        ],
                    ],
                ],
            ]
        ], $msg);
    }

    public function testMustAddBlocksAndOrAttachments()
    {
        $this->expectException(Exception::class);
        Message::new()->validate();
    }
}
