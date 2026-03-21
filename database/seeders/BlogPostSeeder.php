<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get team member IDs for authors (CEO, CTO, Chief Data Scientist, Head of AI Research)
        $authorIds = Team::whereIn('role', [
            'Chief Executive Officer',
            'Chief Technology Officer',
            'Chief Data Scientist',
            'Head of AI Research',
            'Director of Cybersecurity',
        ])->pluck('id', 'role')->toArray();

        $posts = [
            [
                'title' => 'The Future of AI in Enterprise Consulting',
                'excerpt' => 'As artificial intelligence continues to reshape industries, enterprise consulting firms must evolve their approach to deliver AI-driven value. We explore the emerging trends and what they mean for the consulting landscape.',
                'content' => "Artificial intelligence is no longer a futuristic concept — it is the cornerstone of modern enterprise strategy. At Gamma Neutral Consulting, we have witnessed firsthand how AI is transforming the way organizations make decisions, optimize operations, and create competitive advantages.\n\nThe consulting industry stands at an inflection point. Traditional advisory models built on human expertise alone are giving way to hybrid approaches that combine deep domain knowledge with machine learning, natural language processing, and predictive analytics. Clients no longer want just recommendations — they want data-backed insights delivered in real time.\n\nThree key trends are shaping AI in consulting: First, the democratization of AI tools means that even mid-market companies can leverage sophisticated models. Second, the rise of generative AI is enabling consultants to prototype solutions faster than ever before. Third, the growing emphasis on explainable AI ensures that recommendations are transparent and trustworthy.\n\nAt Gamma Neutral, we are investing heavily in these areas. Our data science team has developed proprietary frameworks that accelerate AI adoption for our clients, reducing implementation timelines by 40% while maintaining the highest standards of data governance and security. The future belongs to firms that can bridge the gap between cutting-edge technology and practical business outcomes.",
                'category' => 'AI & Technology',
                'tags' => ['AI', 'Enterprise', 'Consulting', 'Machine Learning', 'Strategy'],
                'status' => 'published',
                'published_at' => '2025-10-15 09:00:00',
                'author_role' => 'Chief Executive Officer',
                'view_count' => 1247,
            ],
            [
                'title' => 'Zero-Trust Security: A Comprehensive Implementation Guide',
                'excerpt' => 'Traditional perimeter-based security is no longer sufficient. This guide walks through the principles, architecture, and practical steps for implementing a zero-trust security framework in your organization.',
                'content' => "The traditional castle-and-moat approach to cybersecurity has become obsolete. In a world of remote work, cloud computing, and sophisticated threat actors, organizations must adopt a zero-trust model that assumes breach and verifies every access request regardless of its origin.\n\nZero-trust is not a single product — it is an architectural philosophy built on three pillars: verify explicitly, use least-privilege access, and assume breach. Implementing this requires a systematic approach that touches identity management, network segmentation, data protection, and continuous monitoring.\n\nAt Gamma Neutral, our cybersecurity practice has guided dozens of organizations through zero-trust adoption. The journey typically begins with a thorough assessment of the current security posture, mapping all data flows and access patterns. From there, we implement micro-segmentation to limit lateral movement, deploy identity-based access controls with multi-factor authentication, and establish continuous verification through behavioral analytics.\n\nThe results speak for themselves: our clients have seen mean-time-to-detect drop from days to minutes, and security incidents have fallen to near zero. The key is treating zero-trust as a journey, not a destination — continuously refining policies, monitoring threats, and adapting to the evolving landscape.",
                'category' => 'Cybersecurity',
                'tags' => ['Cybersecurity', 'Zero-Trust', 'Security Architecture', 'Compliance'],
                'status' => 'published',
                'published_at' => '2025-11-02 10:00:00',
                'author_role' => 'Director of Cybersecurity',
                'view_count' => 892,
            ],
            [
                'title' => 'Data-Driven Decision Making: Beyond Dashboards',
                'excerpt' => 'Dashboards are just the beginning. True data-driven decision making requires a cultural shift, robust data governance, and the right technology stack. Here is how leading organizations are making it happen.',
                'content' => "Every organization claims to be data-driven, but few truly are. The gap between having dashboards and actually embedding data into every decision is vast. At Gamma Neutral, we have observed that the most successful data transformations address three dimensions simultaneously: technology, process, and culture.\n\nTechnology alone does not make an organization data-driven. We have seen companies invest millions in business intelligence platforms only to see adoption rates below 20%. The root cause is almost always the same: data quality issues erode trust, workflows do not integrate analytics into decision points, and leadership does not model data-driven behavior.\n\nThe organizations that succeed start with data governance — establishing clear ownership, quality standards, and access policies. They then embed analytics into existing workflows rather than creating separate reporting environments. Most importantly, they cultivate a culture where questioning assumptions with data is celebrated, not threatening.\n\nOur approach at Gamma Neutral focuses on quick wins that build momentum. We identify three to five high-impact decision points where better data can produce measurable results within 90 days. These early successes create organizational buy-in for broader transformation. The key insight is that data-driven culture is built one decision at a time.",
                'category' => 'Data Analytics',
                'tags' => ['Data Analytics', 'Business Intelligence', 'Data Governance', 'Digital Transformation'],
                'status' => 'published',
                'published_at' => '2025-11-20 08:30:00',
                'author_role' => 'Chief Data Scientist',
                'view_count' => 1034,
            ],
            [
                'title' => 'Cloud Migration Strategies: Lessons from 50+ Enterprise Projects',
                'excerpt' => 'After leading over 50 cloud migration projects, we share the patterns, pitfalls, and proven strategies that separate successful migrations from costly failures.',
                'content' => "Cloud migration remains one of the most requested services in enterprise consulting, yet it is also one of the most frequently mismanaged. After leading over 50 cloud migration projects across financial services, healthcare, government, and retail, our team has distilled the essential patterns that differentiate successful migrations from costly setbacks.\n\nThe first lesson is that migration is a business initiative, not a technology project. Organizations that frame migration around business outcomes — reduced time to market, improved scalability, lower total cost of ownership — consistently outperform those focused purely on technical lift-and-shift. Every workload should be evaluated through the lens of business value.\n\nThe second lesson is the importance of the application assessment phase. We use a 6R framework: Rehost, Replatform, Repurchase, Refactor, Retire, and Retain. Properly categorizing each application prevents the common mistake of over-engineering simple workloads or under-investing in critical ones.\n\nThe third lesson is about people and process. The most technically sound migration will fail if operations teams are not trained, runbooks are not updated, and monitoring is not in place before cutover. We always insist on a parallel-run period and have saved clients millions by catching issues before full cutover. The cloud is not a destination — it is an operating model.",
                'category' => 'Cloud Computing',
                'tags' => ['Cloud Migration', 'AWS', 'Azure', 'Enterprise Architecture', 'Infrastructure'],
                'status' => 'published',
                'published_at' => '2025-12-05 09:00:00',
                'author_role' => 'Chief Technology Officer',
                'view_count' => 756,
            ],
            [
                'title' => 'Practical Applications of Large Language Models in Business',
                'excerpt' => 'Beyond the hype, large language models offer concrete business value. We examine five proven use cases where LLMs are delivering measurable ROI for our enterprise clients.',
                'content' => "Large language models have captured the public imagination, but for enterprise leaders, the question is not whether LLMs are impressive — it is where they deliver measurable business value. After deploying LLM-based solutions for clients across multiple industries, we have identified five use cases with proven ROI.\n\nFirst, document intelligence: automating the extraction and summarization of information from contracts, reports, and regulatory filings. Our financial services clients have reduced document processing time by 70% while improving accuracy. Second, knowledge management: enabling employees to query vast internal knowledge bases in natural language, dramatically reducing time to find information.\n\nThird, customer interaction analytics: analyzing support tickets, call transcripts, and feedback at scale to identify trends and improve service quality. Fourth, code generation and review: accelerating software development cycles while maintaining quality standards. Fifth, regulatory compliance monitoring: continuously scanning regulatory changes and mapping them to organizational obligations.\n\nThe key to success in each case is not the model itself but the surrounding infrastructure: data pipelines that ensure quality inputs, guardrails that prevent hallucination in critical contexts, human-in-the-loop workflows for high-stakes decisions, and robust evaluation frameworks that measure actual business impact rather than just technical metrics.",
                'category' => 'AI & Technology',
                'tags' => ['LLM', 'AI', 'Enterprise Applications', 'NLP', 'Innovation'],
                'status' => 'published',
                'published_at' => '2026-01-10 10:00:00',
                'author_role' => 'Head of AI Research',
                'view_count' => 1523,
            ],
            [
                'title' => 'Building a Data Governance Framework That Actually Works',
                'excerpt' => 'Most data governance initiatives fail within the first year. We share our proven framework that balances rigor with pragmatism to achieve lasting data quality improvements.',
                'content' => "Data governance has a reputation problem. Too often, it is associated with bureaucratic overhead, endless committee meetings, and policies that no one follows. Yet without governance, data quality degrades, compliance risks grow, and analytics initiatives fail to deliver trusted insights.\n\nAt Gamma Neutral, we have developed a governance framework that works because it starts with value rather than control. Instead of launching a comprehensive governance program across all data assets simultaneously, we focus on the data domains that directly impact business outcomes. This targeted approach generates quick wins and builds organizational support.\n\nOur framework has four pillars: ownership (every data domain has a clearly accountable steward), quality (automated profiling and validation at ingestion points), access (self-service catalogs with role-based controls), and lineage (full traceability from source to consumption). Each pillar is implemented incrementally, starting with the highest-value data domains.\n\nThe cultural dimension is equally important. We train data stewards as enablers rather than gatekeepers, implement data quality scorecards that create healthy competition between teams, and celebrate improvements publicly. Organizations that follow this approach achieve measurable improvements in data quality within 90 days and build sustainable governance practices that scale.",
                'category' => 'Data Analytics',
                'tags' => ['Data Governance', 'Data Quality', 'Compliance', 'Best Practices'],
                'status' => 'published',
                'published_at' => '2026-02-14 09:00:00',
                'author_role' => 'Chief Data Scientist',
                'view_count' => 621,
            ],
            [
                'title' => 'The Rise of Edge Computing in Industrial IoT',
                'excerpt' => 'Edge computing is revolutionizing how manufacturers process data from IoT devices. We explore the architecture patterns and business cases driving adoption in Industry 4.0.',
                'content' => "As manufacturing embraces Industry 4.0, the limitations of cloud-only architectures become apparent. When a production line generates gigabytes of sensor data per second and decisions must be made in milliseconds, sending data to the cloud and waiting for a response is simply not viable. This is where edge computing transforms the game.\n\nEdge computing places processing power close to the data source — on the factory floor, in the sensor gateway, or in a local micro-data center. This enables real-time inference for quality control, predictive maintenance, and safety monitoring without the latency penalty of cloud round-trips.\n\nAt Gamma Neutral, we design hybrid edge-cloud architectures that give our manufacturing clients the best of both worlds. Time-critical inference happens at the edge with sub-millisecond latency, while the cloud handles model training, historical analytics, and cross-plant optimization. Our reference architecture includes edge ML runtime, local data buffering for resilience, and secure synchronization with cloud analytics platforms.\n\nThe business case is compelling: our clients have achieved 45% reduction in unplanned downtime through edge-based predictive maintenance, 30% improvement in quality control through real-time visual inspection, and 20% energy savings through edge-optimized process control. As 5G networks mature, the opportunities for edge computing will only expand.",
                'category' => 'IoT & Manufacturing',
                'tags' => ['Edge Computing', 'IoT', 'Manufacturing', 'Industry 4.0', 'Digital Twins'],
                'status' => 'draft',
                'published_at' => null,
                'author_role' => 'Chief Technology Officer',
                'view_count' => 0,
            ],
            [
                'title' => 'Navigating Canada\'s Evolving Data Privacy Landscape',
                'excerpt' => 'With CPPA on the horizon and PIPEDA under review, Canadian businesses must prepare for stricter data privacy requirements. Here is what you need to know and how to get ready.',
                'content' => "Canada\'s data privacy landscape is evolving rapidly. With the proposed Consumer Privacy Protection Act (CPPA) set to modernize PIPEDA, organizations must prepare for stricter consent requirements, expanded individual rights, and significant penalties for non-compliance.\n\nThe key changes include a new Tribunal with order-making powers and the ability to impose penalties of up to 5% of global revenue, stronger consent provisions requiring clear and plain language, enhanced individual rights including data portability and the right to disposal, and new obligations around algorithmic transparency and automated decision-making.\n\nFor consulting firms like Gamma Neutral, these changes present both a challenge and an opportunity. Our clients need to understand not just the legal requirements but the practical implications for their data infrastructure, analytics pipelines, and customer interactions.\n\nOur privacy readiness program takes a risk-based approach: we assess current data practices against the new requirements, identify gaps in consent management and data lifecycle processes, implement technical controls for data portability and deletion, and establish governance frameworks that ensure ongoing compliance. The organizations that start preparing now will have a significant competitive advantage when the new regime takes effect.",
                'category' => 'Compliance',
                'tags' => ['Privacy', 'CPPA', 'PIPEDA', 'Compliance', 'Canada', 'Data Protection'],
                'status' => 'draft',
                'published_at' => null,
                'author_role' => 'Chief Executive Officer',
                'view_count' => 0,
            ],
        ];

        foreach ($posts as $postData) {
            $authorRole = $postData['author_role'];
            unset($postData['author_role']);

            $authorId = $authorIds[$authorRole] ?? null;

            BlogPost::create(array_merge($postData, [
                'slug' => Str::slug($postData['title']),
                'author_id' => $authorId,
            ]));
        }
    }
}
