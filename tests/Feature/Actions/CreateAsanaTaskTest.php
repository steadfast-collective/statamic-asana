<?php

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Statamic\Forms\Form;
use Statamic\Forms\Submission;
use SteadfastCollective\StatamicAsana\Actions\CreateAsanaTask;
use SteadfastCollective\StatamicAsana\DTO\AsanaTaskData;
use SteadfastCollective\StatamicAsana\Tests\TestCase;

class CreateAsanaTaskTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'statamic-asana.api_personal_access_token' => $this->faker->uuid(),
            'statamic-asana.workspace_gid' => $this->faker->uuid(),
            'statamic-asana.project_gid' => $this->faker->uuid(),
            'statamic-asana.assignee_gid' => $this->faker->uuid(),
        ]);
    }

    public function test_it_calls_the_asana_api()
    {
        Http::fake();

        $action = new CreateAsanaTask;

        $data = new AsanaTaskData(
            name: 'Test Task',
            html_notes: '<body>Test Task Notes</body>'
        );

        $action->handle($data);

        Http::assertSent(function ($request) use ($data) {
            $this->assertEquals($request->url(), 'https://app.asana.com/api/1.0/tasks');
            $this->assertEquals('POST', $request->method());

            $this->assertCount(1, $request->header('Authorization'), 'The Authorization header should be set');
            $this->assertEquals(
                'Bearer '.config('statamic-asana.api_personal_access_token'),
                $request->header('Authorization')[0],
                'The Authorization header should contain the personal access token',
            );

            $this->assertEquals([
                'data' => [
                    'name' => $data->name,
                    'html_notes' => $data->html_notes,
                    'resource_subtype' => 'default_task',
                    'completed' => false,
                    'due_on' => now()->addDay()->toDateString(),
                    'projects' => [
                        config('statamic-asana.project_gid'),
                    ],
                    'workspace' => config('statamic-asana.workspace_gid'),
                    'assignee' => config('statamic-asana.assignee_gid'),
                ],
            ],
                $request->data()
            );

            return true;
        });
    }

    /**
     * Test that if the assignee_gid is not set we still make the request
     * (and create an unassigned task).
     */
    public function test_setting_assignee_is_optional()
    {
        config('statamic-asana.assignee_gid', '');

        Http::fake();

        $form = new Form;
        $form->set('name', 'Some Name');

        $submission = new Submission;
        $submission->form($form);

        (new CreateAsanaTask)->handle(
            AsanaTaskData::fromFormSubmission($submission)
        );

        Http::assertSent(function ($request) {
            $this->assertArrayNotHasKey(
                'data.assignee',
                $request->data(),
            );

            return true;
        });
    }

    public function test_it_handles_asana_api_well()
    {
        // TODO: What should we do if the API call fails?
    }
}
