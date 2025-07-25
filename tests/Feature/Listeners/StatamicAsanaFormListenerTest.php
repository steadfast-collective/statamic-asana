<?php

namespace SteadfastCollective\StatamicAsana\Tests\Listners;

use Illuminate\Support\Facades\Queue;
use Statamic\Facades\Form;
use SteadfastCollective\StatamicAsana\Jobs\SendFormDataToAsana;
use SteadfastCollective\StatamicAsana\Listeners\StatamicAsanaFormListener;
use SteadfastCollective\StatamicAsana\Tests\TestCase;

class StatamicAsanaFormListenerTest extends TestCase
{
    public function test_form_submission_queues_job(): void
    {
        Queue::fake();

        $form = Form::make('contact_us');
        $submission = $form->makeSubmission();

        (new StatamicAsanaFormListener)
            ->handle(new \Statamic\Events\FormSubmitted($submission));

        Queue::assertPushed(SendFormDataToAsana::class);
    }

    /**
     * The FormSubmitted listener event should add an array of metadata to the form submission
     */
    public function test_form_listener_adds_meta_data()
    {
        Queue::fake();

        // Add a couple of custom keys to the config to check we read those.
        config([
            'statamic-asana.include_session_data_in_meta' => [
                'landing_page_url',
                'landing_page_referer',
            ],
        ]);

        $form = Form::make('contact_us');

        $submission = $form->makeSubmission();

        (new StatamicAsanaFormListener)
            ->handle(new \Statamic\Events\FormSubmitted($submission));

        Queue::assertPushed(SendFormDataToAsana::class, function (SendFormDataToAsana $job) use ($submission) {
            $this->assertEquals($submission, $job->submission);

            $meta = $submission->get('meta');
            $this->assertIsArray($meta);
            $this->assertArrayHasKey('http_referrer', $meta);
            $this->assertArrayHasKey('form_submitted_on', $meta);
            $this->assertArrayHasKey('landing_page_url', $meta);
            $this->assertArrayHasKey('landing_page_referer', $meta);

            return true;
        });

    }
}
