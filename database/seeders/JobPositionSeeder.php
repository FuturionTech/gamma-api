<?php

namespace Database\Seeders;

use App\Models\JobPosition;
use Illuminate\Database\Seeder;

class JobPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            [
                'title' => 'Senior Data Scientist',
                'department' => 'Data Science & AI',
                'location' => 'Toronto, ON (Redpath Ave)',
                'job_type' => 'full_time',
                'is_remote' => false,
                'salary_range' => '$120,000 - $160,000 CAD',
                'experience_required' => '5+ years',
                'posted_date' => '2025-09-15',
                'status' => 'active',
                'summary' => 'Lead machine learning projects and mentor junior data scientists in developing innovative solutions for our Fortune 500 clients.',
                'description' => 'We are seeking an experienced Senior Data Scientist to join our growing team at Gamma Neutral Consulting. In this role, you will lead cutting-edge machine learning projects, develop predictive models, and work directly with clients to transform their data into actionable insights.',
                'responsibilities' => [
                    'Lead end-to-end machine learning projects from conception to deployment',
                    'Develop and implement advanced statistical models and algorithms',
                    'Collaborate with clients to understand business requirements and translate them into data solutions',
                    'Mentor junior data scientists and provide technical guidance',
                    'Present findings and recommendations to executive stakeholders',
                    'Contribute to research papers and thought leadership content',
                ],
                'requirements' => [
                    "Master's or PhD in Computer Science, Statistics, Mathematics, or related field",
                    '5+ years of experience in data science or machine learning roles',
                    'Expert proficiency in Python and R programming languages',
                    'Deep understanding of machine learning algorithms and statistical methods',
                    'Experience with deep learning frameworks (TensorFlow, PyTorch)',
                    'Strong SQL skills and experience with big data technologies',
                ],
                'nice_to_have' => [
                    'Experience in consulting or client-facing roles',
                    'Publications in peer-reviewed journals or conferences',
                    'Knowledge of cloud platforms (AWS, Azure, GCP)',
                ],
                'benefits' => [
                    'Competitive salary with performance bonuses',
                    'Comprehensive health and dental coverage',
                    'RRSP matching up to 6%',
                    'Professional development budget ($5,000/year)',
                    'Flexible work arrangements',
                    '4 weeks vacation plus personal days',
                ],
                'skills' => ['Python', 'Machine Learning', 'Deep Learning', 'Statistics', 'TensorFlow', 'PyTorch', 'SQL'],
            ],
            [
                'title' => 'Machine Learning Engineer',
                'department' => 'Engineering',
                'location' => 'Remote (Canada)',
                'job_type' => 'full_time',
                'is_remote' => true,
                'salary_range' => '$100,000 - $140,000 CAD',
                'experience_required' => '3+ years',
                'posted_date' => '2025-09-18',
                'status' => 'active',
                'summary' => 'Design and implement scalable ML pipelines and deploy models to production environments.',
                'description' => 'Join our engineering team to build robust, scalable machine learning infrastructure. You will work on deploying models to production, optimizing performance, and ensuring reliability of our ML systems.',
                'responsibilities' => [
                    'Design and implement scalable ML pipelines and infrastructure',
                    'Deploy and monitor machine learning models in production',
                    'Optimize model performance and reduce latency',
                    'Build automated testing and validation frameworks',
                    'Implement MLOps best practices and CI/CD pipelines',
                ],
                'requirements' => [
                    "Bachelor's or Master's in Computer Science or related field",
                    '3+ years of experience in ML engineering or similar role',
                    'Strong programming skills in Python and/or Java',
                    'Experience with ML frameworks (TensorFlow, PyTorch, scikit-learn)',
                    'Proficiency in cloud platforms and containerization (Docker, Kubernetes)',
                ],
                'nice_to_have' => [
                    'Experience with streaming data processing',
                    'Familiarity with monitoring tools (Prometheus, Grafana)',
                    'Contributions to open-source projects',
                ],
                'benefits' => [
                    'Competitive remote salary',
                    'Comprehensive health benefits',
                    'RRSP matching',
                    'Home office setup allowance',
                    'Flexible working hours',
                    'Annual company retreats',
                ],
                'skills' => ['TensorFlow', 'MLOps', 'Cloud', 'Python', 'Kubernetes', 'Docker', 'CI/CD', 'AWS'],
            ],
            [
                'title' => 'Cloud Solutions Architect',
                'department' => 'Infrastructure',
                'location' => 'Remote (Canada)',
                'job_type' => 'full_time',
                'is_remote' => true,
                'salary_range' => '$130,000 - $170,000 CAD',
                'experience_required' => '7+ years',
                'posted_date' => '2025-09-10',
                'status' => 'active',
                'summary' => 'Design and implement cloud infrastructure solutions for large-scale data processing and analytics platforms.',
                'description' => 'Lead the design and implementation of cloud infrastructure that powers our data platforms. You will architect scalable, secure, and cost-effective solutions on AWS, Azure, and GCP for enterprise clients.',
                'responsibilities' => [
                    'Design cloud architecture for data-intensive applications',
                    'Implement infrastructure as code using Terraform',
                    'Optimize cloud costs and resource utilization',
                    'Ensure security best practices and compliance',
                    'Lead cloud migration projects for enterprise clients',
                    'Provide technical leadership and mentorship',
                ],
                'requirements' => [
                    "Bachelor's in Computer Science or related field",
                    '7+ years of experience in cloud architecture',
                    'Expert knowledge of AWS, Azure, or GCP',
                    'Strong experience with Infrastructure as Code (Terraform)',
                    'Deep understanding of networking and security',
                    'Cloud certifications (AWS Solutions Architect, etc.)',
                ],
                'nice_to_have' => [
                    'Experience with multi-cloud strategies',
                    'Understanding of compliance frameworks (SOC2, ISO27001)',
                    'Background in financial services or healthcare',
                ],
                'benefits' => [
                    'Highly competitive salary',
                    'Comprehensive benefits package',
                    'RRSP matching',
                    'Professional certification support',
                    'Flexible remote work',
                    'Conference attendance budget',
                ],
                'skills' => ['AWS', 'Azure', 'Kubernetes', 'Terraform', 'DevOps', 'Security', 'Networking'],
            ],
            [
                'title' => 'Data Strategy Consultant',
                'department' => 'Consulting',
                'location' => 'Toronto, ON (Redpath Ave)',
                'job_type' => 'full_time',
                'is_remote' => false,
                'salary_range' => '$95,000 - $130,000 CAD',
                'experience_required' => '4+ years',
                'posted_date' => '2025-09-12',
                'status' => 'active',
                'summary' => 'Help clients develop and implement comprehensive data strategies aligned with business goals.',
                'description' => 'Join our consulting team to help Fortune 500 companies transform their data capabilities. You will work directly with C-level executives to develop data strategies that drive measurable business value.',
                'responsibilities' => [
                    'Conduct data maturity assessments for client organizations',
                    'Develop comprehensive data strategy roadmaps',
                    'Lead client workshops and executive presentations',
                    'Design data governance frameworks',
                    'Manage consulting engagements and deliverables',
                    'Build long-term client relationships',
                ],
                'requirements' => [
                    "Bachelor's degree in Business, Computer Science, or related field",
                    '4+ years of consulting or strategy experience',
                    'Strong understanding of data analytics and AI',
                    'Excellent presentation and communication skills',
                    'Experience with data governance and compliance',
                    'Ability to travel up to 30%',
                ],
                'nice_to_have' => [
                    'MBA or advanced degree',
                    'Management consulting background',
                    'Change management certification',
                ],
                'benefits' => [
                    'Competitive base salary plus bonuses',
                    'Comprehensive health coverage',
                    'RRSP matching',
                    'Professional development programs',
                    'Downtown office location',
                    'Career advancement paths',
                ],
                'skills' => ['Strategy', 'Analytics', 'Consulting', 'Data Governance', 'Project Management'],
            ],
            [
                'title' => 'Cybersecurity Analyst',
                'department' => 'Security',
                'location' => 'Toronto, ON (Redpath Ave)',
                'job_type' => 'full_time',
                'is_remote' => false,
                'salary_range' => '$85,000 - $115,000 CAD',
                'experience_required' => '3+ years',
                'posted_date' => '2025-10-01',
                'status' => 'active',
                'summary' => 'Conduct security assessments, implement threat detection systems, and ensure compliance for client engagements.',
                'description' => 'Join our cybersecurity practice to help enterprise clients protect their data assets and achieve compliance. You will perform security audits, design threat detection frameworks, and implement zero-trust architectures.',
                'responsibilities' => [
                    'Conduct security assessments and penetration testing',
                    'Design and implement threat detection systems',
                    'Develop security policies and compliance frameworks',
                    'Monitor security events and respond to incidents',
                    'Advise clients on zero-trust architecture implementation',
                ],
                'requirements' => [
                    "Bachelor's in Cybersecurity, Computer Science, or related field",
                    '3+ years of experience in cybersecurity',
                    'Knowledge of security frameworks (NIST, ISO 27001)',
                    'Experience with SIEM tools and threat intelligence platforms',
                    'Understanding of cloud security (AWS, Azure)',
                ],
                'nice_to_have' => [
                    'CISSP, CEH, or CompTIA Security+ certification',
                    'Experience with SOC operations',
                    'Background in financial services compliance',
                ],
                'benefits' => [
                    'Competitive salary',
                    'Certification support and training budget',
                    'Health and dental coverage',
                    'RRSP matching',
                    'Flexible work arrangements',
                ],
                'skills' => ['Cybersecurity', 'SIEM', 'Penetration Testing', 'ISO 27001', 'Cloud Security', 'NIST'],
            ],
        ];

        foreach ($positions as $position) {
            JobPosition::create($position);
        }
    }
}
