<?php

declare(strict_types=1);

namespace SolumDeSignum\ReComposer\Controllers;

use Illuminate\View\View;
use SolumDeSignum\ReComposer\ReComposer;

class ReComposerController
{
    /**
     * @var ReComposer
     */
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
                'serverExtras' => $this->recomposer->getServerExtras(),
                'laravelExtras' => $this->recomposer->getLaravelExtras(),
                'extraStats' => $this->recomposer->getExtraStats(),
            ]
        );
    }
}
