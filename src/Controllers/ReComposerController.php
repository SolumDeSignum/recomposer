<?php

declare(strict_types=1);

namespace SolumDeSignum\ReComposer\Controllers;

use Illuminate\View\View;
use SolumDeSignum\ReComposer\ReComposer;

use function config;

class ReComposerController
{
    private ReComposer $recomposer;

    /**
     * ReComposerController constructor.
     */
    public function __construct()
    {
        $this->recomposer = new ReComposer();
    }

    /**
     * @return View
     */
    public function index(): View
    {

        return view(
            config('recomposer.view', 'solumdesignum/recomposer::index'),
            [
                'packages' => $this->recomposer->packages,
                'laravelEnv' => $this->recomposer->laravelEnvironment(),
                'serverEnv' => $this->recomposer->serverEnvironment(),
                'serverExtras' => $this->recomposer->serverExtras(),
                'laravelExtras' => $this->recomposer->laravelExtras(),
                'extraStats' => $this->recomposer->extraStats(),
                'iconCheck' => config('recomposer.icon.check'),
                'iconUncheck' => config('recomposer.icon.uncheck'),
            ]
        );
    }
}
