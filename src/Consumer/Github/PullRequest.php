<?php

namespace App\Consumer\Github;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use App\ActionHandler\TaskInterface;

class PullRequest implements ConsumerInterface
{
    /**
     * @var array
     */
    private $actions;

    public function __construct(
        array $actions = []
    ) {
        $this->actions = $actions;
    }

    /**
     * @param AMQPMessage $msg
     * @return void
     */
    public function execute(AMQPMessage $msg)
    {
        $body = json_decode($msg->body, true);
        if (!isset($this->actions[$body['action']])) {
            return;
        }

        /** @var TaskInterface $task */
        foreach ($this->actions[$body['action']] as $task) {
            $task->execute($body);
        }
    }
}
