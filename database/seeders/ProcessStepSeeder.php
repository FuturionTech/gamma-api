<?php

namespace Database\Seeders;

use App\Models\ProcessStep;
use App\Models\ProcessStepItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProcessStepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $processSteps = [
            [
                'step_number' => 1,
                'title' => 'Discovery',
                'short_description' => 'We begin by understanding your unique challenges, data landscape, and business objectives.',
                'description' => 'We begin by understanding your unique challenges, data landscape, and business objectives. Through comprehensive analysis and stakeholder engagement, we identify opportunities and define clear goals.',
                'icon' => 'magnifying-glass',
                'icon_color' => '#1E1E1E',
                'order' => 1,
                'items' => [
                    ['title' => 'Requirements Analysis', 'icon' => 'check', 'order' => 1],
                    ['title' => 'Current State Assessment', 'icon' => 'check', 'order' => 2],
                    ['title' => 'Stakeholder Interviews', 'icon' => 'check', 'order' => 3],
                ],
            ],
            [
                'step_number' => 2,
                'title' => 'Solution Design',
                'short_description' => 'Our experts architect comprehensive solutions aligned with your goals and industry best practices.',
                'description' => 'Our experts architect comprehensive solutions aligned with your goals and industry best practices. We create detailed roadmaps and select the right technologies to meet your specific needs.',
                'icon' => 'lightbulb',
                'icon_color' => '#8B5CF6',
                'order' => 2,
                'items' => [
                    ['title' => 'Architecture Planning', 'icon' => 'check', 'order' => 1],
                    ['title' => 'Technology Selection', 'icon' => 'check', 'order' => 2],
                    ['title' => 'Roadmap Creation', 'icon' => 'check', 'order' => 3],
                ],
            ],
            [
                'step_number' => 3,
                'title' => 'Development',
                'short_description' => 'Using agile methodologies, we build robust solutions with continuous testing and refinement.',
                'description' => 'Using agile methodologies, we build robust solutions with continuous testing and refinement. Our development process ensures high quality, scalability, and alignment with your business objectives.',
                'icon' => 'code-bracket',
                'icon_color' => '#3B82F6',
                'order' => 3,
                'items' => [
                    ['title' => 'Agile Development', 'icon' => 'check', 'order' => 1],
                    ['title' => 'Quality Assurance', 'icon' => 'check', 'order' => 2],
                    ['title' => 'CI/CD Pipeline', 'icon' => 'check', 'order' => 3],
                ],
            ],
            [
                'step_number' => 4,
                'title' => 'Deployment',
                'short_description' => 'Seamless deployment with comprehensive training ensures your team maximizes value.',
                'description' => 'Seamless deployment with comprehensive training ensures your team maximizes value. We handle production releases, provide thorough documentation, and train your team for success.',
                'icon' => 'rocket-launch',
                'icon_color' => '#F59E0B',
                'order' => 4,
                'items' => [
                    ['title' => 'Production Release', 'icon' => 'check', 'order' => 1],
                    ['title' => 'User Training', 'icon' => 'check', 'order' => 2],
                    ['title' => 'Documentation', 'icon' => 'check', 'order' => 3],
                ],
            ],
            [
                'step_number' => 5,
                'title' => 'Support',
                'short_description' => 'Our partnership continues with dedicated support and optimization for sustained success.',
                'description' => 'Our partnership continues with dedicated support and optimization for sustained success. We provide ongoing monitoring, performance tuning, and regular updates to ensure your solution evolves with your needs.',
                'icon' => 'lifebuoy',
                'icon_color' => '#10B981',
                'order' => 5,
                'items' => [
                    ['title' => '24/7 Monitoring', 'icon' => 'check', 'order' => 1],
                    ['title' => 'Performance Tuning', 'icon' => 'check', 'order' => 2],
                    ['title' => 'Regular Updates', 'icon' => 'check', 'order' => 3],
                ],
            ],
            [
                'step_number' => 6,
                'title' => 'Results',
                'short_description' => 'Our proven process delivers measurable outcomes for your business.',
                'description' => 'Our proven process delivers tangible results: faster time to market, reduced operational costs, improved data quality, and competitive advantage.',
                'icon' => 'star',
                'icon_color' => '#000000',
                'order' => 6,
                'items' => [
                    ['title' => 'Faster Time to Market', 'icon' => 'arrow-right', 'order' => 1],
                    ['title' => 'Reduced Operational Costs', 'icon' => 'arrow-right', 'order' => 2],
                    ['title' => 'Improved Data Quality', 'icon' => 'arrow-right', 'order' => 3],
                    ['title' => 'Competitive Advantage', 'icon' => 'arrow-right', 'order' => 4],
                ],
            ],
        ];

        foreach ($processSteps as $stepData) {
            $items = $stepData['items'] ?? [];
            unset($stepData['items']);

            $step = ProcessStep::create([
                'title' => $stepData['title'],
                'description' => $stepData['description'],
                'short_description' => $stepData['short_description'],
                'step_number' => $stepData['step_number'],
                'icon' => $stepData['icon'],
                'icon_color' => $stepData['icon_color'],
                'slug' => Str::slug($stepData['title']),
                'order' => $stepData['order'],
                'is_active' => true,
            ]);

            foreach ($items as $item) {
                ProcessStepItem::create([
                    'process_step_id' => $step->id,
                    'title' => $item['title'],
                    'icon' => $item['icon'],
                    'order' => $item['order'],
                ]);
            }
        }
    }
}
