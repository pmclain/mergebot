<?php
declare(strict_types=1);

namespace App\EventHandler\Github;

use App\EventHandler\EventHandlerInterface;
use App\Exception\EventNotFoundException;
use OldSound\RabbitMqBundle\RabbitMq\Producer;

class PullRequest implements EventHandlerInterface
{
    /**
     * @var array
     */
    private $actions;

    /**
     * @var Producer
     */
    private $producer;

    public function __construct(
        array $actions,
        Producer $producer
    ) {
        $this->actions = $actions;
        $this->producer = $producer;
    }

    /**
     * @param array $data
     * @throws EventNotFoundException
     */
    public function handle(array $data): void
    {
        if (!isset($this->actions[$data['action']])) {
            throw new EventNotFoundException(sprintf('Pull request action %s is not supported.', $data['action']));
        }

        $this->producer->setContentType('application/json');
        $this->producer->publish(json_encode($data) ?: '');
    }
}
