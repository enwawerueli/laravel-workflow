<?php

declare(strict_types=1);

namespace EmzD\Workflow;

use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Interface WorkflowServiceInterface
 */
interface WorkflowServiceInterface {
    /**
     * Get workflow for subject
     * 
     * @param object $subject A supported object type i.e has workflow configured
     * @param bool $rebuild Whether to build the workflow afresh
     * 
     * @return \Symfony\Component\Workflow\WorkflowInterface
     */
    public function get(object $subject, ?string $workflowName = null, bool $rebuild = false): WorkflowInterface;

    /**
     * Check if subject is supported by any of the congigured workflows
     * 
     * @param object $subject
     * 
     * @return bool true if subject is supported otherwise false
     */
    public function supports(object $subject, ?string $workflowName = null): bool;
}