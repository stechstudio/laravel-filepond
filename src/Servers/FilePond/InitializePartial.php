<?php

namespace STS\UploadServer\Servers\FilePond;

use Illuminate\Http\Request;
use STS\UploadServer\Events\ChunkedUploadStarted;
use STS\UploadServer\Servers\AbstractStep;
use STS\UploadServer\Storage\PartialFile;

class InitializePartial extends AbstractStep
{
    use PayloadHelper;

    public static function handles(Request $request): bool
    {
        return $request->method() == 'POST' && $request->hasHeader('Upload-Length');
    }

    public function handle()
    {
        $this->file = PartialFile::initialize();
    }

    public function announce()
    {
        event(new ChunkedUploadStarted($this->file, $this->meta));
    }
}
