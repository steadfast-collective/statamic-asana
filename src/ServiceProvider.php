<?php

namespace SteadfastCollective\StatamicAsana;

use Statamic\Events\FormSubmitted;
use Statamic\Providers\AddonServiceProvider;
use SteadfastCollective\StatamicAsana\Listeners\StatamicAsanaFormListener;

class ServiceProvider extends AddonServiceProvider
{
    protected $listen = [
        FormSubmitted::class => [
            StatamicAsanaFormListener::class,
        ],
    ];

    public function bootAddon(): void
    {
        //     \Statamic\Facades\Form::appendConfigFields('*', __('Configuration of something else'), [
        //         'data' => [
        //             'type' => 'grid',
        //             'mode' => 'stacked',
        //             'add_row' => __('Add Field'),
        //             'fields' => [
        //                 [
        //                     'handle' => 'handle',
        //                     'field' => [
        //                         'type' => 'slug',
        //                         'display' => __('Handle'),
        //                         'validate' => [
        //                             'required',
        //                         ],
        //                     ],
        //                 ],
        //                 [
        //                     'handle' => 'value',
        //                     'field' => [
        //                         'type' => 'text',
        //                         'display' => __('Value'),
        //                         'validate' => [
        //                             'required',
        //                         ],
        //                     ],
        //                 ],
        //             ],
        //         ],
        //     ]);
    }
}
