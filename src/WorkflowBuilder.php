<?php

declare(strict_types=1);

namespace EmzD\Workflow;

use EmzD\Workflow\Events\WorkflowEventListener;
use EmzD\Workflow\Models\Workflow as Model;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Metadata\InMemoryMetadataStore;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Validator\WorkflowValidator;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Trait WorkflowBuilder
 */
class WorkflowBuilder {
    public function build(Model $model): WorkflowInterface
    {
        $definition = $this->buildDefinition($model);
        $singleState = $model->singleState();
        $this->validateDefinition($definition, $singleState, $model->name);
        $markingStore = new MethodMarkingStore($singleState, $model->marking_property ?: 'marking');
        $dispatcher = new EventDispatcher();
        $eventsToDispatch = $this->setupEventListener($model, $dispatcher);
        return new Workflow($definition, $markingStore, $dispatcher, $model->name, $eventsToDispatch);
    }

    public function buildDefinition(Model $model): Definition
    {
        $builder = new DefinitionBuilder();
        $placesMetadata = [];
        $builder->addPlaces($model->places->map(function ($p) use (&$placesMetadata) {
            if ($p->metadata) {
                $placesMetadata[$p->name] = $p->metadata;
            }
            return $p->name;
        })->all());
        $builder->setInitialPlaces($model->initialPlaces->map(function ($p) {
            return $p->name;
        })->all());
        $transitionsMetadata = new \SplObjectStorage();
        $builder->addTransitions($model->transitions->map(function ($t) use (&$transitionsMetadata) {
            ($froms = $t->from->map(function ($p) {
                return $p->name;
            })->all());
            ($tos = $t->to->map(function ($p) {
                return $p->name;
            })->all());
            $transition = new Transition($t->name, $froms, $tos);
            if ($t->metadata) {
                $transitionsMetadata->attach($transition, $t->metadata);
            }
            return $transition;
        }
        )->all());
        $builder->setMetadataStore(new InMemoryMetadataStore($model->metadata ?: [], $placesMetadata, $transitionsMetadata));
        return $builder->build();
    }

    public function validateDefinition(Definition $definition, bool $singlePlace, string $workflowName): void
    {
        (new WorkflowValidator($singlePlace))->validate($definition, $workflowName);
    }

    private function setupEventListener(Model $model, EventDispatcherInterface $dispatcher): array
    {
        $guards = [];
        foreach ($model->transitions as $t) {
            if ($t->guard) {
                $guards[sprintf('workflow.%s.guard.%s', $model->name, $t->name)] = $t->guard;
            }
        }
        $listner = new WorkflowEventListener($guards);
        $eventsToDispatch = [];
        foreach ($model->events as $event) {
            $eventsToDispatch[] = sprintf('workflow.%s', $event->name);
            $dotEvents = [];
            $dotEvents[] = sprintf('workflow.%s', $event->name);
            $dotEvents[] = sprintf('workflow.%s.%s', $model->name, $event->name);
            foreach ($model->{$event->scope} as $x) {
                $dotEvents[] = sprintf('workflow.%s.%s.%s', $model->name, $event->name, $x->name);
            }
            if (!method_exists($listner, $method = 'on' . ucfirst($event->name))) {
                $method = 'onEvent';
            }
            foreach ($dotEvents as $dotEvent) {
                $dispatcher->addListener($dotEvent, [$listner, $method]);
            }
        }
        return $eventsToDispatch;
    }
}