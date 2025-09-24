<?php

namespace SteadfastCollective\StatamicAsana\Actions;

use Exception;
use Illuminate\Support\Facades\Http;
use SteadfastCollective\StatamicAsana\DTO\AsanaTaskData;

/**
 * Given an AsanaTaskData object - make the API request to to create the task in Asana.
 */
class CreateAsanaTask
{
    private AsanaTaskData $asanaTaskData;

    /**
     * Create a task and return the permalink URL of the created task.
     */
    public function handle(AsanaTaskData $asanaTaskData): string
    {
        $this->asanaTaskData = $asanaTaskData;

        $this->validateConfig();

        return $this->sendApiRequest();
    }

    /**
     * Send the request to Asana
     *
     * @see https://developers.asana.com/reference/createtask
     */
    public function sendApiRequest(): string
    {
        $response = Http::withToken(config('statamic-asana.api_personal_access_token'))
            ->post('https://app.asana.com/api/1.0/tasks', [
                'data' => $this->getData(),
            ]);

        if ($response->failed()) {
            // TODO: Include an anonymous reference to the form submission for debugging.
            throw new \Exception('Failed to create Asana Task: '.$response->body());
        }

        return $response->json('data.permalink_url');
    }

    /**
     * Check that the required configuration variables is set. Throws an exception if not.
     */
    public function validateConfig(): void
    {
        throw_unless(
            filled(config('statamic-asana.api_personal_access_token')),
            'Asana setting api_personal_access_token is not set in config/statamic-asana.php'
        );

        throw_unless(
            filled(config('statamic-asana.workspace_gid')),
            'Asana setting workspace_gid is not set in config/statamic-asana.php'
        );

        throw_unless(
            filled(config('statamic-asana.project_gid')),
            'Asana setting project_gid is not set in config/statamic-asana.php'
        );
    }

    /**
     * Build the data parameter which we send to Asana
     *
     * @see https://developers.asana.com/reference/createtask
     */
    public function getData(): array
    {
        $data = [
            'resource_subtype' => 'default_task',
            'completed' => false,
            'due_on' => now()->addDay()->toDateString(),
            'workspace' => config('statamic-asana.workspace_gid'),
            'memberships' => [
                [
                    'project' => config('statamic-asana.project_gid'),
                ],
            ],

            'name' => $this->asanaTaskData->name,
            'html_notes' => $this->asanaTaskData->html_notes,
        ];

        // Assignee is optional
        if (filled($assigneeGid = config('statamic-asana.assignee_gid'))) {
            $data['assignee'] = $assigneeGid;
        }

        // Section is optional
        if (filled($sectionGid = config('statamic-asana.section_gid'))) {
            $data['memberships'][0]['section'] = $sectionGid;
        }

        return $data;
    }
}
