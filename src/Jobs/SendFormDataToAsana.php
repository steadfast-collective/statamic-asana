<?php

namespace SteadfastCollective\StatamicAsana\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Statamic\Contracts\Forms\Submission;
use SteadfastCollective\StatamicAsana\Actions\CreateAsanaTask;
use SteadfastCollective\StatamicAsana\DTO\AsanaTaskData;

/**
 * ShouldBeEncrypted is used because the queue contains sensitive data.
 */
class SendFormDataToAsana implements ShouldBeEncrypted, ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    public function __construct(
        public Submission $submission,
    ) {}

    public function handle(): void
    {
        $data = AsanaTaskData::fromFormSubmission($this->submission);

        $action = new CreateAsanaTask;

        $action->handle($data);
    }
}
