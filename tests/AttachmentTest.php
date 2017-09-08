<?php 

namespace Lumenpress\Fluid\Tests;

use Lumenpress\Fluid\Models\Attachment;

class AttachmentTest extends TestCase
{
    /**
     * @group attachment
     */
    public function testAttachment()
    {
        $attachment = new Attachment;
        $attachment->file = '/Users/chen/laradock/wwwroot/bet/public/bet/build/assets/data/1.jpg';

        d($attachment);
    }
}
