<?php

namespace EmzD\Workflow\Events;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\TransitionBlocker;

/**
 * WorkflowEventListener
 */
class WorkflowEventListener {
    public function __construct(private array $guards)
    {
        # code...
    }

    public function onEvent(Event $event, string $eventName, EventDispatcherInterface $dispatcher)
    {
        event($eventName, $event);
    }

    public function onGuard(Event $event, string $eventName, EventDispatcherInterface $dispatcher)
    {
        if (array_key_exists($eventName, $this->guards)) {
            $expression = $this->guards[$eventName];
            $variables = [
                'subject' => $event->getSubject()
            ];
            $expressionLanguage = new (config('emzd_workflow.expression_language', ExpressionLanguage::class));
            if (!$expressionLanguage->evaluate($expression, $variables)) {
                $event->addTransitionBlocker(TransitionBlocker::createBlockedByExpressionGuardListener($expression));
            }
        }
        event($eventName, $event);
    }
}