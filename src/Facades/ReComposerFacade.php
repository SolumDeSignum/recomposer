<?php

declare(strict_types=1);

namespace SolumDeSignum\ReComposer\Facades;

use SolumDeSignum\ReComposer\ReComposer;

class ReComposerFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ReComposer::class;
    }
}
