<?php

namespace Augusthur\FirmarIntegration\StoreAdapter;

use Augusthur\FirmarIntegration\StoreAdapterInterface;

class CallbackAdapter implements StoreAdapterInterface
{
    protected $setCallback;
    protected $getCallback;

    public function __construct__(callable $setCallback, callable $getCallback)
    {
        $this->setCallback = $setCallback;
        $this->getCallback = $getCallback;
    }

    public function get()
    {
        $result = call_user_func($this->getCallback);
        return $result ?? [
            'expired' => true,
        ];
    }

    public function set($token)
    {
        return call_user_func($this->setCallback, $token);
    }
}
