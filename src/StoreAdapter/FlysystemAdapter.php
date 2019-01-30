<?php

namespace Augusthur\FirmarIntegration\StoreAdapter;

use Augusthur\FirmarIntegration\StoreAdapterInterface;

class FlysystemAdapter implements StoreAdapterInterface
{
    protected $filesystem;
    protected $path;

    public function __construct__($filesystem, $path)
    {
        $this->filesystem = $filesystem;
        $this->path = $path;
    }

    public function get()
    {
        if ($this->filesystem->has($path)) {
            $token = json_decode($this->filesystem->read($path), true);
            $token['expired'] = time() > $token['expires_at'];
        } else {
            $token = [
                'expired' => true,
            ];
        }
        return $token;
    }

    public function set($token)
    {
        return $this->filesystem->put($path, json_encode($token));
    }
}
