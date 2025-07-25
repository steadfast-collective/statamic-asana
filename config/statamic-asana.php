<?php

return [
    'api_personal_access_token' => env('STATAMIC_ASANA_API_PERSONAL_ACCESS'),
    'workspace_gid' => env('STATAMIC_ASANA_WORKSPACE_GID'),
    'project_gid' => env('STATAMIC_ASANA_PROJECT_GID'),

    /**
     * The value of these keys will be included in the meta data collected saved
     * with the form.
     */
    'include_session_data_in_meta' => [
        'landing_page_url',
        'landing_page_referer',
    ],
];
