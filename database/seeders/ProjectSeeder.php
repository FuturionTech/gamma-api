<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'title' => 'Digital Transformation for National Bank of Canada',
                'description' => 'Comprehensive digital transformation initiative for one of Canada\'s leading banks, modernizing legacy data systems and implementing real-time analytics across all business units.',
                'challenge' => 'The bank\'s legacy infrastructure was siloed across 12 departments, with inconsistent data formats and no centralized analytics capability. Risk reporting took 48 hours and compliance gaps were growing.',
                'solution' => 'We designed and implemented a unified data lake on AWS, built real-time ETL pipelines with Apache Kafka, and deployed a centralized business intelligence platform with role-based dashboards for every department.',
                'results' => 'Risk reporting time reduced from 48 hours to 15 minutes. Achieved 99.9% data consistency across all departments. $4.2M annual savings from operational efficiencies. Full regulatory compliance achieved 3 months ahead of deadline.',
                'featured_image_url' => 'https://ui-avatars.com/api/?name=National+Bank&size=800&background=8b5cf6&color=fff',
                'client_name' => 'National Bank of Canada',
                'industry' => 'Financial Services',
                'technologies' => ['AWS', 'Apache Kafka', 'Snowflake', 'Python', 'Tableau', 'Terraform'],
                'status' => 'published',
                'completion_date' => '2025-06-15',
            ],
            [
                'title' => 'Cloud Migration for Provincial Healthcare Network',
                'description' => 'Migrated a provincial healthcare network\'s on-premise infrastructure to a HIPAA-compliant cloud environment, enabling secure data sharing across 28 hospitals and 150 clinics.',
                'challenge' => 'The healthcare network operated on aging on-premise servers with limited redundancy. Patient data was fragmented across facilities, and inter-facility data sharing was manual and error-prone.',
                'solution' => 'Executed a phased cloud migration to Azure with zero downtime. Implemented FHIR-compliant data integration APIs, encrypted data pipelines, and a unified patient record system accessible across all facilities.',
                'results' => 'Zero-downtime migration completed in 8 months. Patient record retrieval time reduced by 85%. Annual infrastructure costs reduced by 35%. Achieved HIPAA and PIPEDA compliance across all facilities.',
                'featured_image_url' => 'https://ui-avatars.com/api/?name=Healthcare+Network&size=800&background=10b981&color=fff',
                'client_name' => 'Ontario Provincial Health Network',
                'industry' => 'Healthcare',
                'technologies' => ['Azure', 'FHIR', 'Kubernetes', 'PostgreSQL', 'Docker', 'Terraform'],
                'status' => 'published',
                'completion_date' => '2025-03-20',
            ],
            [
                'title' => 'AI-Powered Analytics for Retail Chain',
                'description' => 'Developed and deployed a machine learning-powered customer analytics and demand forecasting platform for a national retail chain with 200+ stores.',
                'challenge' => 'The retailer experienced 15% inventory waste and frequent stockouts. Customer segmentation was based on basic demographics with no behavioral analysis, resulting in poor marketing ROI.',
                'solution' => 'Built a recommendation engine using collaborative filtering and deep learning, integrated real-time POS data with an analytics platform, and deployed demand forecasting models across all product categories.',
                'results' => 'Inventory waste reduced by 40%. Customer conversion rates increased by 35%. Marketing ROI improved by 28%. Demand forecasting accuracy reached 94%, up from 72%.',
                'featured_image_url' => 'https://ui-avatars.com/api/?name=Retail+Analytics&size=800&background=f59e0b&color=fff',
                'client_name' => 'MapleLeaf Retail Group',
                'industry' => 'Retail',
                'technologies' => ['Python', 'TensorFlow', 'Apache Spark', 'Redis', 'Power BI', 'AWS SageMaker'],
                'status' => 'published',
                'completion_date' => '2025-08-10',
            ],
            [
                'title' => 'Smart City Analytics Platform for Metropolitan Toronto',
                'description' => 'Designed and deployed a comprehensive IoT and analytics platform for municipal operations, covering traffic management, utility optimization, and citizen service delivery.',
                'challenge' => 'The city managed traffic, utilities, and citizen services through disconnected legacy systems. Emergency response times were above national averages, and there was no data-driven approach to urban planning.',
                'solution' => 'Deployed a city-wide IoT sensor network integrated with a real-time analytics platform on AWS GovCloud. Built predictive models for traffic flow, energy consumption, and emergency response optimization.',
                'results' => 'Traffic congestion reduced by 30% on major corridors. Emergency response times improved by 22%. Energy consumption in public buildings reduced by 18%. Citizen satisfaction scores increased by 25%.',
                'featured_image_url' => 'https://ui-avatars.com/api/?name=Smart+City&size=800&background=3b82f6&color=fff',
                'client_name' => 'City of Toronto - Innovation Division',
                'industry' => 'Government',
                'technologies' => ['AWS GovCloud', 'IoT Hub', 'Apache Kafka', 'MongoDB', 'Grafana', 'Python'],
                'status' => 'published',
                'completion_date' => '2025-01-30',
            ],
            [
                'title' => 'Predictive Maintenance for Automotive Manufacturer',
                'description' => 'Implemented an Industry 4.0 predictive maintenance system across 3 manufacturing plants, leveraging IoT sensors and machine learning to prevent equipment failures.',
                'challenge' => 'Unplanned equipment downtime was costing $2.8M annually. Maintenance was reactive, and there was no visibility into equipment health until failures occurred.',
                'solution' => 'Installed 2,000+ IoT sensors on critical equipment, built real-time data pipelines to a central analytics platform, and trained custom ML models for failure prediction with 72-hour advance warning.',
                'results' => 'Unplanned downtime reduced by 45%. Maintenance costs reduced by $1.6M annually. Equipment lifespan extended by an average of 18%. ROI achieved within 9 months of deployment.',
                'featured_image_url' => 'https://ui-avatars.com/api/?name=Predictive+Maintenance&size=800&background=6b7280&color=fff',
                'client_name' => 'Canadian Auto Manufacturing Corp.',
                'industry' => 'Manufacturing',
                'technologies' => ['Azure IoT', 'Digital Twins', 'Python', 'MQTT', 'Grafana', 'Kubernetes'],
                'status' => 'published',
                'completion_date' => '2024-11-15',
            ],
            [
                'title' => 'Zero-Trust Security Framework for Insurance Provider',
                'description' => 'Designed and implemented a comprehensive zero-trust security architecture for a major insurance provider, protecting sensitive client data across hybrid cloud infrastructure.',
                'challenge' => 'The insurer had experienced two security incidents in 18 months. Their perimeter-based security model could not protect against insider threats or lateral movement within the network.',
                'solution' => 'Implemented a zero-trust architecture with micro-segmentation, identity-based access controls, continuous verification, and 24/7 SOC monitoring with AI-powered threat detection.',
                'results' => 'Zero security incidents since implementation. Mean time to detect threats reduced from 72 hours to 4 minutes. Achieved SOC 2 Type II and ISO 27001 certification. Insurance premiums reduced by 20%.',
                'featured_image_url' => 'https://ui-avatars.com/api/?name=Zero+Trust&size=800&background=ef4444&color=fff',
                'client_name' => 'Northern Shield Insurance',
                'industry' => 'Financial Services',
                'technologies' => ['CrowdStrike', 'Splunk', 'Azure AD', 'HashiCorp Vault', 'Palo Alto', 'Terraform'],
                'status' => 'published',
                'completion_date' => '2025-05-22',
            ],
            [
                'title' => 'Learning Analytics Platform for University Consortium',
                'description' => 'Built a unified learning analytics platform for a consortium of 5 universities, enabling data-driven insights into student performance, retention, and curriculum effectiveness.',
                'challenge' => 'Each university used different LMS platforms with siloed data. There was no cross-institutional visibility into student outcomes, making it impossible to identify at-risk students early or optimize curricula.',
                'solution' => 'Created a federated data architecture respecting each university\'s data sovereignty while enabling cross-institutional analytics. Deployed predictive models for student retention and adaptive learning path recommendations.',
                'results' => 'Early identification of at-risk students improved by 60%. Graduation rates increased by 12% over two academic years. Curriculum optimization reduced redundant courses by 15%. Platform adopted by all 5 universities with 45,000+ active students.',
                'featured_image_url' => 'https://ui-avatars.com/api/?name=Learning+Analytics&size=800&background=8b5cf6&color=fff',
                'client_name' => 'Ontario University Consortium',
                'industry' => 'Education',
                'technologies' => ['Python', 'R', 'Snowflake', 'xAPI', 'React', 'AWS'],
                'status' => 'published',
                'completion_date' => '2025-09-01',
            ],
        ];

        foreach ($projects as $projectData) {
            Project::create(array_merge($projectData, [
                'slug' => Str::slug($projectData['title']),
            ]));
        }
    }
}
