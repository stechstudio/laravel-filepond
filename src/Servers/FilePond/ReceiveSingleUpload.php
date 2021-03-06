<?php

namespace STS\UploadServer\Servers\FilePond;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use STS\UploadServer\Events\UploadComplete;
use STS\UploadServer\Servers\AbstractStep;
use STS\UploadServer\Storage\File;

class ReceiveSingleUpload extends AbstractStep
{
    protected $event = UploadComplete::class;

    public static function handles(Request $request): bool
    {
        return count($request->allFiles()) > 0;
    }

    public function handle()
    {
        $this->file = File::storeUploadedFile($this->findFile());
    }

    public function percentComplete(): int
    {
        return 100;
    }

    protected function findFile(): UploadedFile
    {
        $file = current($this->request->allFiles());

        if (is_array($file)) {
            $file = current($file);
        }

        return $file;
    }
}
