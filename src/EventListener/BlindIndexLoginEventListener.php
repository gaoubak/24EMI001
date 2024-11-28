<?php

namespace App\EventListener;

use App\Service\BlindIndexService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[AsEventListener(event: RequestEvent::class, priority: 11, method: 'onKernelRequest')]
class BlindIndexLoginEventListener
{
    public function __construct(private BlindIndexService $blindIndexService)
    {
        
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if ($request->get('_route') === 'api_login_check') {
            $data = json_decode($request->getContent(), true);
            $data['email'] = $this->blindIndexService->getBlindIndex($data['email']);

            $request->initialize(
                $request->query->all(),
                $request->request->all(),
                $request->attributes->all(),
                $request->cookies->all(),
                $request->files->all(),
                $request->server->all(),
                json_encode($data)
            );
        }
    }
}