<?php

namespace App\Controller;

use App\EventHandler\EventHandlerPoolInterface;
use App\Exception\EventNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Github
{
    const HEADER_EVENT = 'X-GitHub-Event';

    /**
     * @var EventHandlerPoolInterface
     */
    private $handlerPool;

    public function __construct(
        EventHandlerPoolInterface $handlerPool
    ) {
        $this->handlerPool = $handlerPool;
    }

    public function webhook(Request $request): JsonResponse
    {
        try {
            $event = $request->headers->get(self::HEADER_EVENT);
            if (!$event) {
                throw new EventNotFoundException(sprintf('%s header contained no event.', self::HEADER_EVENT));
            }

            $this->handlerPool->handle(
                $event,
                json_decode($request->getContent(), true)
            );
        } catch (EventNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }

        return new JsonResponse(['status' => 'ok'], 202);
    }
}
