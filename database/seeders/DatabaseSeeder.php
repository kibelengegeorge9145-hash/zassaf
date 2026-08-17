<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Event;
use App\Models\MembershipPlan;
use App\Models\Program;
use App\Models\Setting;
use App\Models\User;
use App\Models\WeekendConvo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $adminName = env('ADMIN_NAME', 'Zassaf Admin');
        $adminUsername = env('ADMIN_USERNAME', 'zassaf');
        $adminEmail = env('ADMIN_EMAIL', 'admin@zassaf.com');
        $adminPassword = env('ADMIN_PASSWORD', 'password');

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'username' => $adminUsername,
                'phone' => '+255 7XX XXX XXX',
                'password' => Hash::make($adminPassword),
                'role' => User::ROLE_SUPER_ADMIN,
                'is_active' => true,
            ]
        );

        $settings = [
            'org_name' => 'Zassaf Elite Community',
            'motto' => 'Think. Grow. Lead.',
            'tagline' => 'The Zassaf Elite Community is a growing network of ambitious young Africans committed to knowledge, personal growth and leadership.',
            'contact_phone' => '+255 7XX XXX XXX',
            'contact_email' => 'hello@zassaf.com',
            'contact_address' => 'Dar es Salaam, Tanzania',
            'whatsapp_url' => 'https://wa.me/2557XXXXXXX',
            'email' => 'hello@zassaf.com',
            'instagram_url' => 'https://instagram.com/zassaf',
            'facebook_url' => 'https://facebook.com/zassaf',
            'tiktok_url' => 'https://tiktok.com/@zassaf',
            'telegram_url' => 'https://t.me/zassafelitecommunity',
            'membership_status' => 'coming_soon',
            'membership_launch_date' => '2027-01-01',
            'membership_registration_fee' => '10000',
            'membership_monthly_fee' => '5000',
            'membership_currency' => 'TZS',
            'membership_registration_open' => '0',
            'membership_payment_enabled' => '0',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $programs = [
            [
                'slug' => 'leadership-personal-growth',
                'title_en' => 'Leadership & Personal Growth',
                'title_sw' => 'Uongozi na Ukuzaji Binafsi',
                'description_en' => 'Practical leadership training and mentorship that help you lead yourself first, then influence others with integrity.',
                'description_sw' => 'Mafunzo ya uongozi na ushauri wa vitendo unaokusaidia kujiongoza wewe mwenyewe kwanza, kisha kuwashawishi wengine kwa uadilifu.',
                'icon' => 'shield',
            ],
            [
                'slug' => 'skills-career-development',
                'title_en' => 'Skills & Career Development',
                'title_sw' => 'Ustadi na Ukuzaji wa Kazi',
                'description_en' => 'High-impact skills, career guidance and exposure opportunities that prepare you to compete and thrive in the modern world.',
                'description_sw' => 'Ustadi wa kiwango cha juu, mwongozo wa kazi na fursa za kujifunza zinazokuandaa kushindana na kufanikiwa katika dunia ya kisasa.',
                'icon' => 'rocket',
            ],
            [
                'slug' => 'entrepreneurship',
                'title_en' => 'Entrepreneurship',
                'title_sw' => 'Ujasiriamali',
                'description_en' => 'Workshops, case studies and coaching that turn ideas into ventures and help young founders build sustainable businesses.',
                'description_sw' => 'Semina, mifano halisi na mafunzo yanayogeuza mawazo kuwa biashara na kuwasaidia wajasiriamali vijana kujenga biashara endelevu.',
                'icon' => 'lightbulb',
            ],
            [
                'slug' => 'youth-empowerment',
                'title_en' => 'Youth Empowerment',
                'title_sw' => 'Uwezeshaji wa Vijana',
                'description_en' => 'Community programs and advocacy that give young Africans a platform to speak, serve and shape the future they want to see.',
                'description_sw' => 'Programu za jamii na utetezi zinazowapa vijana wa Afrika jukwaa la kusema, kutumikia na kuunda maisha wanayotamani.',
                'icon' => 'users',
            ],
        ];

        foreach ($programs as $program) {
            Program::updateOrCreate(['slug' => $program['slug']], $program);
        }

        WeekendConvo::updateOrCreate(
            ['slug' => 'why-you-should-start-today'],
            [
                'title_en' => 'Why You Should Start Today',
                'title_sw' => 'Kwa Nini Uanze Leo',
                'description_en' => 'A live conversation about overcoming procrastination, building momentum and why the best time to begin is now.',
                'description_sw' => 'Mazungumzo ya moja kwa moja kuhusu kuondokana na tabia ya kuahirisha, kujenga kasi na kwa nini wakati mwafaka wa kuanza ni sasa.',
                'topics_en' => "Mindset\nAction\nConsistency",
                'topics_sw' => "Mtazamo\nKitendo\nMsimamo",
                'event_date' => now()->addDays(10)->toDateString(),
                'event_time' => '10:00 AM - 12:00 PM',
                'platform_en' => 'Live on Zoom',
                'platform_sw' => 'Moja kwa moja kupitia Zoom',
                'speaker_en' => 'Zassaf Community',
                'speaker_sw' => 'Jumuiya ya Zassaf',
                'is_published' => true,
            ]
        );

        Book::updateOrCreate(
            ['slug' => 'start-before-youre-ready'],
            [
                'title_en' => 'Start Before You\'re Ready',
                'title_sw' => 'Anza Kabla Huja Jitayarisha',
                'description_en' => 'A bold guide for young Africans who want to take action, embrace discomfort and build the future they deserve — even when they don\'t feel ready yet.',
                'description_sw' => 'Mwongozo wa kijasiri kwa vijana wa Afrika wanaotaka kuchukua hatua, kukumbatia changamoto na kujenga maisha wanayostahili — hata wakati hawajahisi tayari.',
                'author' => 'Zassaf Elite Community',
                'status' => 'preorder',
                'publication_date' => now()->addMonths(2)->toDateString(),
                'price' => 15000,
                'currency' => 'TZS',
                'preorder_enabled' => true,
                'is_featured' => true,
            ]
        );

        Event::updateOrCreate(
            ['title_en' => 'Zassaf Launch Gathering'],
            [
                'title_en' => 'Zassaf Launch Gathering',
                'title_sw' => 'Mkutano wa Uzinduzi wa Zassaf',
                'description_en' => 'An evening to launch the community, meet like-minded young leaders and kick off our 2027 membership journey.',
                'description_sw' => 'Jioni ya kuzindua jumuiya, kukutana na vijana wenzako wenye maono na kuanzisha safari ya uanachama ya 2027.',
                'event_date' => now()->addDays(21)->toDateString(),
                'event_time' => '4:00 PM - 7:00 PM',
                'location_en' => 'Dar es Salaam, Tanzania',
                'location_sw' => 'Dar es Salaam, Tanzania',
                'is_published' => true,
            ]
        );

        $plans = [
            [
                'name_en' => 'Membership Registration Fee',
                'name_sw' => 'Ada ya Usajili wa Uanachama',
                'description_en' => 'One-time registration fee to join the Zassaf Elite Community and access all member benefits.',
                'description_sw' => 'Ada ya usajili ya mara moja ya kujiunga na Zassaf Elite Community na kupata manufaa yote ya wanachama.',
                'price' => 10000,
                'registration_fee' => 10000,
                'monthly_fee' => 0,
                'currency' => 'TZS',
                'billing_cycle' => 'one_time',
                'status' => MembershipPlan::STATUS_ACTIVE,
                'is_active' => true,
            ],
            [
                'name_en' => 'Monthly Membership',
                'name_sw' => 'Uanachama wa Kila Mwezi',
                'description_en' => 'Recurring monthly contribution that keeps the community running and funds programs, books and events.',
                'description_sw' => 'Mchango wa kila mwezi unaoendesha jumuiya na kufadhili programu, vitabu na matukio.',
                'price' => 5000,
                'registration_fee' => 0,
                'monthly_fee' => 5000,
                'currency' => 'TZS',
                'billing_cycle' => 'monthly',
                'status' => MembershipPlan::STATUS_ACTIVE,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            MembershipPlan::updateOrCreate(
                ['name_en' => $plan['name_en'], 'billing_cycle' => $plan['billing_cycle']],
                $plan
            );
        }
    }
}
