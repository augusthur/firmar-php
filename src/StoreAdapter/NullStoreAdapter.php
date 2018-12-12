<?php

namespace Augusthur\FirmarIntegration\StoreAdapter;

use Augusthur\FirmarIntegration\StoreAdapterInterface;

class NullStoreAdapter implements StoreAdapterInterface
{
    public function get()
    {
        return [
            'expired' => true,
        ];
    }

    public function set($token)
    {
        return true;
    }
}
