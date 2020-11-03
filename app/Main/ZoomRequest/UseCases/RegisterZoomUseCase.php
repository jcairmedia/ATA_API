<?php

namespace App\Main\ZoomRequest\UseCases;

use App\Main\ZoomRequest\Domain\ZoomRequestDomain;
use App\ZoomRequest;

class RegisterZoomUseCase
{
    public function __construct(ZoomRequestDomain $zoomrequest)
    {
        $this->zoomrequest = $zoomrequest;
    }

    public function __invoke(ZoomRequest $zoom)
    {
        $this->zoomrequest->add($zoom);
    }
}
