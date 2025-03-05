<?php

namespace Coolsam\VisualForms\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Coolsam\VisualForms\VisualForms
 */
class VisualForms extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Coolsam\VisualForms\VisualForms::class;
    }
}
