<?php

namespace STS\UploadServer\Servers;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use STS\UploadServer\Storage\File;
use STS\UploadServer\Servers\AbstractServer;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractStep implements Responsable
{
    /** @var Request */
    protected $request;

    /** @var AbstractServer */
    protected $server;

    /** @var File */
    protected $file;

    /** @var array */
    protected $meta;

    /** @var string */
    protected $event;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function run(AbstractServer $server): Responsable
    {
        $this->server = $server;
        $this->meta = $server->meta();

        $this->handle();
        $this->announce();

        if($this->isFinished()) {
            $this->finalize();
        }

        return $this;
    }

    abstract public static function handles(Request $request): bool;

    abstract public function handle();

    abstract public function percentComplete(): int;

    public function isFinished(): bool
    {
        return $this->percentComplete() == 100;
    }

    public function finalize()
    {
    }

    public function announce()
    {
        $class = $this->event;

        event(new $class($this->file, $this, $this->meta));
    }

    public function response()
    {
        return $this->textResponse($this->file->id());
    }

    protected function textResponse($text): Response
    {
        return response($text)->header('Content-Type', 'text/plain');
    }

    public function whenFinished(\Closure $callable): AbstractStep
    {
        if ($this->isFinished()) {
            $callable($this->file, $this);
        }

        return $this;
    }

    public function toResponse($request)
    {
        return $this->response();
    }

    public function server(): AbstractServer
    {
        return $this->server;
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function file(): File
    {
        return $this->file;
    }

    public function meta(): array
    {
        return $this->meta;
    }
}
