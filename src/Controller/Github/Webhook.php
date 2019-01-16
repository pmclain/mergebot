<?php
declare(strict_types=1);

namespace App\Controller\Github;

use App\Entity\EventRecorder;
use App\EventHandler\EventHandlerPoolInterface;
use App\Exception\EventNotFoundException;
use App\Exception\RequestValidationException;
use App\Github\RequestValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Webhook
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

    /**
     * @var EventRecorder
     */
    private $eventRecorder;

    public function __construct(
        EventHandlerPoolInterface $handlerPool,
        RequestValidator $requestValidator,
        EventRecorder $eventRecorder
    ) {
        $this->handlerPool = $handlerPool;
        $this->requestValidator = $requestValidator;
        $this->eventRecorder = $eventRecorder;
    }

    public function execute(Request $request): JsonResponse
    {
        $eventData = json_decode($request->getContent(), true);

        try {
            $this->requestValidator->validate($request);
            $event = $request->headers->get(self::HEADER_EVENT);
            if (!$event) {
                throw new EventNotFoundException(sprintf('%s header contained no event.', self::HEADER_EVENT));
            }

            if (is_array($event)) {
                throw new \InvalidArgumentException('Event expected to be string, array provided.');
            }

            $this->handlerPool->handle($event, $eventData);
        } catch (EventNotFoundException $e) {
            $this->eventRecorder->record('webhook', $eventData, $e->getMessage());
            return new JsonResponse(['error' => $e->getMessage()], 400);
        } catch (RequestValidationException $e) {
            $this->eventRecorder->record('webhook', $eventData, $e->getMessage());
            return new JsonResponse(['error' => $e->getMessage()], 403);
        }

        return new JsonResponse(['status' => 'ok'], 202);
    }
}
