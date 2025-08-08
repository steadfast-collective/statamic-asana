<?php

namespace SteadfastCollective\StatamicAsana\Tests\Feature\DTO;

use Statamic\Facades\Form;
use SteadfastCollective\StatamicAsana\Tests\TestCase;

class AsanaTaskDataTest extends TestCase
{
    /**
     * Test that when we create an AsanaTaskData object from form data all the fields
     * are included in the Asana task - even arbitrary ones we don't know about.
     */
    public function test_task_html_notes_includes_all_fields()
    {
        $form = Form::make('contact_us');
        /** @var \Statamic\Forms\Form $form */
        $form->title('Contact Us')
            ->honeypot('winnie')
            ->data();

        $form->save();

        $submission = $form->makeSubmission();
        $submission->data([
            'name' => 'Some Name',
            'message' => 'This is a test message.',
            'field_unique_to_this_form' => 'An unexpected field',
        ]);

        $taskData = \SteadfastCollective\StatamicAsana\DTO\AsanaTaskData::fromFormSubmission(
            $submission
        );

        $this->assertEquals('New Contact From: Some Name', $taskData->name);

        $this->assertStringContainsString(
            'This is a test message.',
            $taskData->html_notes
        );

        $this->assertStringContainsString(
            'field_unique_to_this_form',
            $taskData->html_notes
        );

        $this->assertStringContainsString(
            'An unexpected field',
            $taskData->html_notes
        );
    }

    public function test_task_html_notes_handles_array_values()
    {
        $form = Form::make('contact_us');
        /** @var \Statamic\Forms\Form $form */
        $form->title('Contact Us');
        $form->data();

        $form->save();

        $submission = $form->makeSubmission();
        $submission->data([
            'name' => 'Some Name',
            'multiple_options' => ['Foo', 'Bar'],
        ]);

        $taskData = \SteadfastCollective\StatamicAsana\DTO\AsanaTaskData::fromFormSubmission(
            $submission
        );

        $this->assertStringContainsString(
            '<li>Foo</li><li>Bar</li>',
            $taskData->html_notes
        );
    }

    public function test_meta_is_rendered_separately()
    {
        $form = Form::make('contact_us');
        /** @var \Statamic\Forms\Form $form */
        $form->title('Contact Us');
        $form->save();

        $submission = $form->makeSubmission();
        $submission->data([
            'meta' => [
                'source' => 'website',
                'referrer' => 'example.com',
            ],
        ]);

        $taskData = \SteadfastCollective\StatamicAsana\DTO\AsanaTaskData::fromFormSubmission(
            $submission
        );

        $this->assertStringContainsString(
            '<h1>Meta Data</h1><h2>source</h2>website<h2>referrer</h2>example.com',
            $taskData->html_notes
        );
    }
}
