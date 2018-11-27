<?php

namespace App\Controller;

use App\EventHandler\EventHandlerPoolInterface;
use App\Exception\EventNotFoundException;
use App\Github\RequestValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Github
{
    const HEADER_EVENT = 'X-GitHub-Event';

    /**
     * @var EventHandlerPoolInterface
     */
    private $handlerPool;

    /**
     * @var RequestValidator
     */
    private $requestValidator;

    public function __construct(
        EventHandlerPoolInterface $handlerPool,
        RequestValidator $requestValidator
    ) {
        $this->handlerPool = $handlerPool;
        $this->requestValidator = $requestValidator;
    }

    public function webhook(Request $request): JsonResponse
    {
        try {
            $this->requestValidator->validate($request);
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
        } catch (UnauthorizedHttpException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 403);
        }

        return new JsonResponse(['status' => 'ok'], 202);
    }
}
