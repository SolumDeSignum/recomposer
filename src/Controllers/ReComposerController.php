<?php

declare(strict_types=1);

namespace SolumDeSignum\ReComposer\Controllers;

use Illuminate\View\View;
use SolumDeSignum\ReComposer\ReComposer;

use function config;

class ReComposerController
{
    /**
     * @param ReComposer $recomposer
     * @return View
     */
    public function index(ReComposer $recomposer): View
    {
        return view(
            config('recomposer.view', 'solumdesignum/recomposer::index'),
            [
                'packages' => $recomposer->packages,
                'laravelEnv' => $recomposer->laravelEnvironment(),
                'serverEnv' => $recomposer->serverEnvironment(),
                'serverExtras' => $recomposer->serverExtras(),
                'laravelExtras' => $recomposer->laravelExtras(),
                'extraStats' => $recomposer->extraStats(),
                'iconCheck' => config('recomposer.icon.check'),
                'iconUncheck' => config('recomposer.icon.uncheck'),
            ]
        );
    }
}
