<?php

namespace Database\Seeders;

use App\Models\Solution;
use App\Models\SolutionFeature;
use App\Models\SolutionBenefit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SolutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $solutions = [
            [
                'title' => 'Financial Services',
                'subtitle' => 'Transform your financial operations with intelligent data solutions',
                'description' => 'Our comprehensive suite of financial data solutions helps banks, investment firms, and fintech companies leverage advanced analytics to enhance decision-making, reduce risk, and improve operational efficiency.',
                'industry_category' => 'FINANCIAL_SERVICES',
                'icon' => 'bank',
                'icon_color' => '#10B981',
                'order' => 1,
                'features' => [
                    ['title' => 'Risk Analytics', 'description' => 'Advanced risk modeling and assessment using machine learning algorithms to predict and mitigate financial risks in real-time.', 'icon' => 'chart-line', 'order' => 1],
                    ['title' => 'Fraud Detection', 'description' => 'Intelligent fraud detection systems that identify suspicious patterns and anomalies across millions of transactions.', 'icon' => 'shield-check', 'order' => 2],
                    ['title' => 'Regulatory Compliance', 'description' => 'Automated compliance monitoring and reporting to ensure adherence to financial regulations and standards.', 'icon' => 'clipboard-check', 'order' => 3],
                    ['title' => 'Portfolio Optimization', 'description' => 'Data-driven portfolio management strategies that maximize returns while minimizing risk exposure.', 'icon' => 'trending-up', 'order' => 4],
                ],
                'benefits' => [
                    ['title' => 'Reduced Operational Risk', 'description' => 'Minimize financial losses through predictive analytics and early warning systems.', 'icon' => 'shield', 'order' => 1],
                    ['title' => 'Enhanced Decision Making', 'description' => 'Make informed decisions with real-time insights and comprehensive data analysis.', 'icon' => 'lightbulb', 'order' => 2],
                    ['title' => 'Improved Compliance', 'description' => 'Stay ahead of regulatory requirements with automated compliance tracking.', 'icon' => 'check-circle', 'order' => 3],
                    ['title' => 'Cost Efficiency', 'description' => 'Reduce operational costs through process automation and optimization.', 'icon' => 'dollar', 'order' => 4],
                ],
            ],
            [
                'title' => 'Smart Government',
                'subtitle' => 'Empowering public services with data-driven insights',
                'description' => 'Transform government operations with secure, scalable data infrastructures that improve citizen services, enhance transparency, and optimize resource allocation across public sector organizations.',
                'industry_category' => 'GOVERNMENT',
                'icon' => 'landmark',
                'icon_color' => '#3B82F6',
                'order' => 2,
                'features' => [
                    ['title' => 'Public Analytics', 'description' => 'Comprehensive analytics for citizen services, policy-making, and resource optimization.', 'icon' => 'chart-bar', 'order' => 1],
                    ['title' => 'Citizen Services', 'description' => 'Digital platforms that improve public service delivery and citizen engagement.', 'icon' => 'users', 'order' => 2],
                    ['title' => 'Policy Insights', 'description' => 'Data-driven insights to inform policy decisions and measure their impact.', 'icon' => 'document-text', 'order' => 3],
                ],
                'benefits' => [
                    ['title' => 'Transparency', 'description' => 'Open and accountable government operations with real-time reporting.', 'icon' => 'eye', 'order' => 1],
                    ['title' => 'Efficiency', 'description' => 'Streamlined processes that save time and taxpayer money.', 'icon' => 'zap', 'order' => 2],
                ],
            ],
            [
                'title' => 'Healthcare Analytics',
                'subtitle' => 'Revolutionizing patient care with data and AI',
                'description' => 'Leverage AI and big data solutions to improve patient outcomes, optimize clinical operations, and reduce healthcare costs while maintaining the highest standards of data security and privacy.',
                'industry_category' => 'HEALTHCARE',
                'icon' => 'hospital',
                'icon_color' => '#EC4899',
                'order' => 3,
                'features' => [
                    ['title' => 'Patient Insights', 'description' => 'Clinical data analytics to improve diagnosis, treatment planning, and patient outcomes.', 'icon' => 'heartbeat', 'order' => 1],
                    ['title' => 'Clinical Data Integration', 'description' => 'Unified view of patient data across systems for better care coordination.', 'icon' => 'database', 'order' => 2],
                    ['title' => 'Operational Optimization', 'description' => 'Optimize hospital operations, staffing, and resource allocation.', 'icon' => 'cog', 'order' => 3],
                ],
                'benefits' => [
                    ['title' => 'Better Care', 'description' => 'Enhanced patient experience and improved health outcomes.', 'icon' => 'heart', 'order' => 1],
                    ['title' => 'Cost Savings', 'description' => 'Reduce healthcare costs through efficiency and preventive care.', 'icon' => 'piggy-bank', 'order' => 2],
                ],
            ],
            [
                'title' => 'Education Analytics',
                'subtitle' => 'AI-powered platforms for personalized learning',
                'description' => 'Transform education with adaptive learning technologies, performance analytics, and data-driven insights that help educators create personalized learning experiences and improve student outcomes.',
                'industry_category' => 'EDUCATION',
                'icon' => 'graduation-cap',
                'icon_color' => '#8B5CF6',
                'order' => 4,
                'features' => [
                    ['title' => 'Student Performance', 'description' => 'Track and analyze student progress with comprehensive performance metrics.', 'icon' => 'chart-line', 'order' => 1],
                    ['title' => 'Learning Optimization', 'description' => 'Personalized learning paths based on individual student needs and abilities.', 'icon' => 'brain', 'order' => 2],
                    ['title' => 'Curriculum Insights', 'description' => 'Data-driven insights to optimize curriculum design and teaching methods.', 'icon' => 'book-open', 'order' => 3],
                ],
                'benefits' => [
                    ['title' => 'Better Outcomes', 'description' => 'Improved student performance and graduation rates.', 'icon' => 'trophy', 'order' => 1],
                    ['title' => 'Scalability', 'description' => 'Grow your educational programs efficiently with technology.', 'icon' => 'arrow-up', 'order' => 2],
                ],
            ],
            [
                'title' => 'Manufacturing 4.0',
                'subtitle' => 'Smart manufacturing through predictive analytics',
                'description' => 'Enable Industry 4.0 transformation with IoT integration, predictive maintenance, supply chain optimization, and real-time production monitoring to maximize efficiency and reduce downtime.',
                'industry_category' => 'MANUFACTURING',
                'icon' => 'cog',
                'icon_color' => '#F59E0B',
                'order' => 5,
                'features' => [
                    ['title' => 'Predictive Maintenance', 'description' => 'AI-powered systems that predict equipment failures before they occur.', 'icon' => 'wrench', 'order' => 1],
                    ['title' => 'Supply Chain Analytics', 'description' => 'Optimize supply chain operations with real-time visibility and insights.', 'icon' => 'truck', 'order' => 2],
                    ['title' => 'Quality Control', 'description' => 'Automated quality inspection and defect detection using computer vision.', 'icon' => 'check-badge', 'order' => 3],
                ],
                'benefits' => [
                    ['title' => 'Reduced Downtime', 'description' => 'Minimize production interruptions with predictive insights.', 'icon' => 'clock', 'order' => 1],
                    ['title' => 'Cost Optimization', 'description' => 'Lower operational costs through automation and efficiency.', 'icon' => 'dollar', 'order' => 2],
                ],
            ],
            [
                'title' => 'Retail Intelligence',
                'subtitle' => 'Data-driven retail optimization',
                'description' => 'Transform retail operations with customer analytics, inventory optimization, and personalized shopping experiences that drive sales, improve customer satisfaction, and maximize profitability.',
                'industry_category' => 'RETAIL',
                'icon' => 'shopping-cart',
                'icon_color' => '#EF4444',
                'order' => 6,
                'features' => [
                    ['title' => 'Customer Analytics', 'description' => 'Deep insights into customer behavior, preferences, and purchasing patterns.', 'icon' => 'users', 'order' => 1],
                    ['title' => 'Inventory Optimization', 'description' => 'Smart inventory management to reduce waste and stockouts.', 'icon' => 'cube', 'order' => 2],
                    ['title' => 'Demand Forecasting', 'description' => 'Accurate demand predictions to optimize stock levels and purchasing.', 'icon' => 'chart-bar', 'order' => 3],
                ],
                'benefits' => [
                    ['title' => 'Increased Sales', 'description' => 'Boost revenue with personalized recommendations and optimized pricing.', 'icon' => 'trending-up', 'order' => 1],
                    ['title' => 'Customer Loyalty', 'description' => 'Build lasting relationships through personalized experiences.', 'icon' => 'heart', 'order' => 2],
                ],
            ],
        ];

        foreach ($solutions as $solutionData) {
            $features = $solutionData['features'] ?? [];
            $benefits = $solutionData['benefits'] ?? [];
            unset($solutionData['features'], $solutionData['benefits']);

            $solution = Solution::create([
                'title' => $solutionData['title'],
                'subtitle' => $solutionData['subtitle'],
                'description' => $solutionData['description'],
                'slug' => Str::slug($solutionData['title']),
                'industry_category' => $solutionData['industry_category'],
                'icon' => $solutionData['icon'],
                'icon_color' => $solutionData['icon_color'],
                'order' => $solutionData['order'],
                'is_active' => true,
            ]);

            // Create features
            foreach ($features as $feature) {
                SolutionFeature::create([
                    'solution_id' => $solution->id,
                    'title' => $feature['title'],
                    'description' => $feature['description'],
                    'icon' => $feature['icon'],
                    'order' => $feature['order'],
                ]);
            }

            // Create benefits
            foreach ($benefits as $benefit) {
                SolutionBenefit::create([
                    'solution_id' => $solution->id,
                    'title' => $benefit['title'],
                    'description' => $benefit['description'],
                    'icon' => $benefit['icon'],
                    'order' => $benefit['order'],
                ]);
            }
        }
    }
}
