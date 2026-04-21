<?php

namespace SteadfastCollective\StatamicAsana\Listeners;

use Statamic\Events\FormSubmitted;
use SteadfastCollective\StatamicAsana\Jobs\SendFormDataToAsana;

/**
 * Listens for form submissions and dispatches a job to send the data to Asana.
 */
class StatamicAsanaFormListener
{
    public function handle(FormSubmitted $event): void
    {
        $meta = [
            'http_referrer' => request()->headers->get('referer', 'none'),
            'form_submitted_on' => request()->url(),
        ];

        foreach (config('statamic-asana.include_session_data_in_meta', []) as $key) {
            $meta[$key] = session($key, 'none');
        }

        $event->submission->set('meta', $meta);

        SendFormDataToAsana::dispatch(
            $event->submission,
        );
    }
}
