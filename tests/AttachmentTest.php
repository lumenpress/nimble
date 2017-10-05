<?php

namespace LumenPress\Nimble\Tests;

use Laravel\Lumen\Application;
use LumenPress\Nimble\Models\Attachment;

class AttachmentTest extends TestCase
{
    /**
     * @group attachment
     */
    public function testAttachment()
    {
        $app = new Application;
        $app->withFacades();
        $app->withEloquent();

        $attachment = new Attachment;
        $attachment->file = 'http://via.placeholder.com/350x150';

        $attachment->save();

        // d($attachment->toArray());
        //
        $this->assertTrue(true, 'message');
    }
}
