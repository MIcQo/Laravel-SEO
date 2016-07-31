<?php

namespace MichalKoval\SEO;

use Illuminate\Support\Facades\Facade;

/**
 * @see \MichalKoval\Seo
 */
class SEOFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'seo';
    }
}
