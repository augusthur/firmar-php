<?php

namespace Augusthur\FirmarIntegration;

interface StoreAdapterInterface
{
    public function get();
    public function set($token);
}

