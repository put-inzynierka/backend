<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class LocaleListener
{
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        $locale = $request->headers->get('Accept-Language', 'pl');
        $locale = substr($locale, 0, 2);
        $locale = strtolower($locale);

        $request->setLocale($locale);
    }
}