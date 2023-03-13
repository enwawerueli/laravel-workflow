<?php

declare(strict_types=1);

namespace EmzD\Workflow;

use EmzD\Workflow\Exceptions\MultipleWorkflowsException;
use EmzD\Workflow\Exceptions\UndefinedWorkflowException;
use EmzD\Workflow\Models\Workflow as WorkflowModel;
use EmzD\Workflow\WorkflowBuilder;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Class WorkflowService
 */
class WorkflowService implements WorkflowServiceInterface {
    private array $workflows = [];
    private WorkflowBuilder $workflowBuilder;

    public function __construct()
    {
        $this->workflowBuilder = new WorkflowBuilder();
    }

    public function get(object $subject, ?string $workflowName = null, bool $rebuild = false): WorkflowInterface
    {
        $model = $this->getModel($subject, $workflowName);
        if (!$rebuild && array_key_exists($model->name, $this->workflows)) {
            return $this->workflows[$model->name];
        }
        $workflow = $this->workflowBuilder->build($model);
        $this->workflows[$model->name] = $workflow;
        return $workflow;
    }

    public function supports(object $subject, ?string $workflowName = null): bool
    {
        return WorkflowModel::whereSupports(get_class($subject))
            ->when($workflowName, function ($query, $workflowName) {
                return $query->whereName($workflowName);
            })
            ->exists();
    }

    private function getModel(object $subject, ?string $workflowName = null)
    {
        $models = WorkflowModel::with([
            'places',
            'initialPlaces',
            'transitions' => ['from', 'to'],
            'eventsToDispatch'
        ])
            ->whereSupports(get_class($subject))
            ->when($workflowName, function ($query, $workflowName) {
                return $query->whereName($workflowName);
            })
            ->get();
        if ($models->isEmpty()) {
            throw new UndefinedWorkflowException(
                sprintf('Unable to find a workflow%s that supports this subject (%s).', $workflowName ? sprintf(' (%s)', $workflowName) : '', get_debug_type($subject))
            );
        }
        if ($models->count() > 1) {
            $names = implode(', ', $models->map(function ($m) {
                return $m->name;
            })->all());
            throw new MultipleWorkflowsException(
                sprintf('Multiple workflows (%s) match this subject (%s); specify the workflow name.', $names, get_debug_type($subject))
            );
        }
        return $models[0];
    }
}