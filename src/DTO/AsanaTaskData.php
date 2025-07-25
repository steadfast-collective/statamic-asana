<?php

namespace SteadfastCollective\StatamicAsana\DTO;

use Statamic\Contracts\Forms\Submission;

/**
 * This DTO represents a Task in Asana - so the field names align with the Asana API.
 */
class AsanaTaskData
{
    public function __construct(
        public string $name,
        public string $html_notes,
    ) {}

    /**
     * Given a form Submission object create an AsanaTaskData object which contains all
     * of it's data.
     *
     * @todo this could be a blade file, and customisable in the config.
     */
    public static function fromFormSubmission(Submission $submission): self
    {
        // Allowed HTML is quite specific: https://developers.asana.com/docs/rich-text
        $html_notes = [];
        $html_notes[] = '<body>';

        $data = collect($submission->data());
        foreach ($data->except('meta') as $field => $value) {
            // TODO Get the field label from the form blueprint instead of using the index.
            $html_notes[] = "<h1>{$field}</h1>";
            if (is_array($value)) {
                $html_notes[] = '<ul>';
                foreach ($value as $item) {
                    $html_notes[] = '<li>'.htmlspecialchars($item).'</li>';
                }
                $html_notes[] = '</ul>';
            } else {
                $html_notes[] = htmlspecialchars($value);
            }
        }

        // Add the meta data, if the key is set.
        if ($data->has('meta')) {
            /** @var array $meta */
            $meta = $data->get('meta');
            $html_notes[] = '<h1>Meta Data</h1>';
            foreach ($meta as $key => $value) {
                $html_notes[] = '<h2>'.htmlspecialchars($key).'</h2>';
                $html_notes[] = htmlspecialchars($value);
            }
        }

        // If we have a meta-data key
        $html_notes[] = '</body>';

        return new self(
            name: 'New Contact From: '.($submission->get('name')),
            html_notes: implode($html_notes),
        );
    }
}
