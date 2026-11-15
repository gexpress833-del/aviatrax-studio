<?php

namespace App\Services;

use App\Enums\ProjectStage;
use App\Models\Project;
use App\Models\ProjectComment;
use Illuminate\Support\Collection;

class ProjectWorkflowService
{
    /**
     * Ordre strict du workflow de production d'un morceau.
     */
    public const WORKFLOW_SEQUENCE = [
        ProjectStage::ENREGISTREMENT,
        ProjectStage::MIXAGE,
        ProjectStage::MASTERING,
        ProjectStage::DISTRIBUTION_EN_COURS,
        ProjectStage::DISTRIBUTION_TERMINEE,
    ];

    public function canAdvance(Project $project, ProjectStage|string $nextStage): bool
    {
        $next = $nextStage instanceof ProjectStage ? $nextStage : ProjectStage::from($nextStage);
        $current = $project->current_stage instanceof ProjectStage ? $project->current_stage : ProjectStage::from($project->current_stage ?? ProjectStage::ENREGISTREMENT->value);

        $currentIndex = array_search($current, self::WORKFLOW_SEQUENCE, true);
        $nextIndex = array_search($next, self::WORKFLOW_SEQUENCE, true);

        if ($currentIndex === false || $nextIndex === false) {
            return false;
        }

        return $nextIndex === $currentIndex + 1;
    }

    public function nextStage(Project $project): ?ProjectStage
    {
        $currentIndex = array_search($project->current_stage, self::WORKFLOW_SEQUENCE, true);

        if ($currentIndex === false || ! isset(self::WORKFLOW_SEQUENCE[$currentIndex + 1])) {
            return null;
        }

        return self::WORKFLOW_SEQUENCE[$currentIndex + 1];
    }

    public function progressLabel(ProjectStage $stage): string
    {
        return $stage->label();
    }

    public function isFinalStage(Project $project): bool
    {
        return $project->current_stage === ProjectStage::DISTRIBUTION_TERMINEE;
    }

    public function addComment(Project $project, int $userId, string $content, string $stage, bool $isApproval = false): ProjectComment
    {
        return $project->comments()->create([
            'user_id' => $userId,
            'content' => $content,
            'stage' => $stage,
            'is_approval' => $isApproval,
        ]);
    }

    public function orderedStages(): Collection
    {
        return collect(self::WORKFLOW_SEQUENCE);
    }
}
