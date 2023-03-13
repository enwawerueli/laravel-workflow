<?php

declare(strict_types=1);

namespace EmzD\Workflow;

use EmzD\Workflow\Events\WorkflowEventListener;
use EmzD\Workflow\Models\Workflow as Model;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Metadata\InMemoryMetadataStore;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Validator\StateMachineValidator;
use Symfony\Component\Workflow\Validator\WorkflowValidator;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Trait WorkflowBuilder
 */
class WorkflowBuilder {
    public function build(Model $model): WorkflowInterface
    {
        $builder = new DefinitionBuilder();
        $placesMetadata = [];
        $builder->addPlaces($places = $model->places->map(function ($p) use (&$placesMetadata) {
            if ($p->metadata) {
                $placesMetadata[$p->name] = $p->metadata;
            }
            return $p->name;
        })->all());
        $builder->setInitialPlaces($model->initialPlaces->map(function ($p) {
            return $p->name;
        })->all());
        $transitions = [];
        $transitionsMetadata = new \SplObjectStorage();
        $guards = [];
        $builder->addTransitions($model->transitions->map(function ($t) use ($model, &$transitions, &$transitionsMetadata, &$guards) {
            $froms = $t->from->map(function ($p) {
                return $p->name;
            }
            )->all();
            $tos = $t->to->map(function ($p) {
                return $p->name;
            }
            )->all();
            $transitions[] = $t->name;
            $transition = new Transition($t->name, $froms, $tos);
            if ($t->metadata) {
                $transitionsMetadata->attach($transition, $t->metadata);
            }
            if ($t->guard) {
                $guards[sprintf('workflow.%s.guard.%s', $model->name, $t->name)] = $t->guard;
            }
            return $transition;
        }
        )->all());
        $builder->setMetadataStore(new InMemoryMetadataStore($model->metadata, $placesMetadata, $transitionsMetadata));
        $definition = $builder->build();
        $validator = ($singleState = $model->type === 'state_machine')
            ? new StateMachineValidator()
            : new WorkflowValidator();
        $validator->validate($definition, $model->name);
        $dispatcher = new EventDispatcher();
        $events = $model->eventsToDispatch->all();
        $this->setupEventListeners($dispatcher, $events, $model->name, $transitions, $places, $guards);
        $markingStore = new MethodMarkingStore($singleState, $model->marking_property);
        $events = array_map(function ($e) {
            return sprintf('workflow.%s', $e->name);
        }, $events);
        return new Workflow($definition, $markingStore, $dispatcher, $model->name, $events);
    }

    private function setupEventListeners(EventDispatcherInterface $dispatcher, array $events, string $workflowName, array $transitions, array $places, array $guards)
    {
        $eventListner = new WorkflowEventListener($guards);
        foreach ($events as $event) {
            $dotEvents = [];
            $dotEvents[] = sprintf('workflow.%s', $event->name);
            $dotEvents[] = sprintf('workflow.%s.%s', $workflowName, $event->name);
            if ($event->scope === 'transition') {
                foreach ($transitions as $transition) {
                    $dotEvents[] = sprintf('workflow.%s.%s.%s', $workflowName, $event->name, $transition);
                }
            }
            if ($event->scope === 'place') {
                foreach ($places as $place) {
                    $dotEvents[] = sprintf('workflow.%s.%s.%s', $workflowName, $event->name, $place);
                }
            }
            foreach ($dotEvents as $dotEvent) {
                if (!method_exists($eventListner, $methodName = 'on' . ucfirst($event->name))) {
                    $methodName = 'onEvent';
                }
                $dispatcher->addListener($dotEvent, $eventListner->{$methodName}(...));
            }
        }
    }
}