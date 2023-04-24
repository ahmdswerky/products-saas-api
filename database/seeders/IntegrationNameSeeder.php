<?php

namespace Database\Seeders;

use App\Models\IntegrationName;
use Illuminate\Database\Seeder;

class IntegrationNameSeeder extends Seeder
{
    protected $names = [
        'other' => [
            'google_analytics' => [
                'name' => 'Google Analytics',
                'description' => 'Keep track of your users activites on Google Analytics.',
                'url' => 'https://analytics.google.com/analytics/web/',
                'icon' => 'google-analytics.png',
                'is_available' => false,
            ],
            'hotjar' => [
                'name' => 'hotjar',
                'description' => 'Get heatmap for of your users.',
                'url' => 'https://www.hotjar.com/',
                'icon' => 'hotjar.svg',
                'is_available' => true,
            ],
            'logrocket' => [
                'name' => 'LogRocket',
                'description' => 'Analyize your users experience through session video recording.',
                'url' => 'https://www.logrocket.com/',
                'icon' => 'logrocket.svg',
                'is_available' => true,
            ],
            'slack' => [
                'name' => 'Slack',
                'description' => 'Get notified about events in your store.',
                'url' => 'https://slack.com/',
                'icon' => 'slack.svg',
                'is_available' => false,
            ],
            'zendesk' => [
                'name' => 'Zendesk',
                'description' => 'Build software to meet customer needs.',
                'url' => 'https://zendesk.com/',
                'icon' => 'zendesk.svg',
                'is_available' => false,
            ],
        ],
        'mail' => [
            'mailchimp' => [
                'name' => 'Mailchimp',
                'description' => 'Engage your customers and boost your business with email campaigns.',
                'url' => 'https://mailchimp.com/',
                'icon' => 'mailchimp.png',
                'is_available' => false,
            ],
        ],
        'chat' => [
            'intercom' => [
                'name' => 'Intercom',
                'description' => 'Provide Chat Support for your users.',
                'url' => 'https://www.intercom.com/',
                'icon' => 'intercom.svg',
                'is_available' => true,
            ],
            'crisp' => [
                'name' => 'Crisp',
                'description' => 'Provide Chat Support for your users.',
                'url' => 'https://crisp.chat/en/',
                'icon' => 'crisp.svg',
                'is_available' => true,
            ],
            'freshwork_chat' => [
                'name' => 'Freshwork Chat',
                'description' => 'Provide Chat Support for your users.',
                'url' => 'https://freshworks.com/',
                'icon' => 'freshworks.svg',
                'is_available' => true,
            ],
            'helpdesk' => [
                'name' => 'HelpDesk',
                'description' => 'Provide Chat Support for your users.',
                'url' => 'https://www.helpdesk.com/',
                'icon' => 'helpdesk.svg',
                'is_available' => false,
            ],
            'helpscout' => [
                'name' => 'HelpScout',
                'description' => 'Provide Chat Support for your users.',
                'url' => 'https://helpscout.com/',
                'icon' => 'helpscout.svg',
                'is_available' => true,
            ],
            // 'chatbot' => [
            //     'name' => 'Chatbot',
            //     'description' => 'Provide Chat Support for your users.',
            //     'url' => 'https://www.chatbot.com/',
            //     'icon' => 'chatbot.svg',
            //     'is_available' => false,
            // ],
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect($this->names)->map(function ($value, $key) {
            collect($value)->map(function ($integration) use ($key) {
                // IntegrationName::create(
                IntegrationName::query()->updateOrCreate(
                    [
                        'name' => $integration['name'],
                    ],
                    array_merge(
                        $integration,
                        ['category' => $key],
                    ),
                );
            });
        });
    }
}
