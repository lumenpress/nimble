<?php

namespace LumenPress\Nimble\Tests;

use LumenPress\Nimble\Models\Attachment;

class AttachmentTest extends TestCase
{
    /**
     * @group attachment
     */
    public function testAttachment()
    {
        $attachment = new Attachment;
        $attachment->file = 'http://via.placeholder.com/350x150';

        $attachment->save();

        // d($attachment->toArray());
        //
        $this->assertTrue(true, 'message');
    }
}
