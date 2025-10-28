<?php include 'header.php'; ?>
<title>About Us - Trade Author | Advanced Crypto & Stock Trading Platform</title>
<style>
    /* Main Content */
    main {
        padding-top: 80px;
    }

    /* Page Hero */
    .page-hero {
        padding: 120px 0 80px;
        text-align: center;
        background: radial-gradient(circle at 50% 50%, rgba(255, 107, 53, 0.1) 0%, transparent 50%);
        position: relative;
        overflow: hidden;
    }

    .page-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="%23ff6b35" opacity="0.03"><polygon points="0,0 1000,50 1000,100 0,100"/></svg>') no-repeat center bottom;
        background-size: cover;
    }

    .page-hero h1 {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        position: relative;
        z-index: 2;
    }

    .page-hero h1 span {
        background: var(--gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .page-hero p {
        font-size: 1.2rem;
        color: var(--text-muted);
        max-width: 600px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
    }

    /* Story Section */
    .story-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
    }

    .story-content h3 {
        font-size: 1.8rem;
        margin-bottom: 20px;
        color: var(--accent);
    }

    .story-content p {
        color: var(--text-muted);
        margin-bottom: 20px;
        line-height: 1.8;
    }

    .story-stats {
        display: flex;
        gap: 30px;
        margin-top: 30px;
    }

    .stat-box {
        text-align: center;
        padding: 20px;
        background: var(--card-bg);
        border-radius: 15px;
        border: 1px solid var(--border);
        backdrop-filter: blur(10px);
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--accent);
        font-family: 'Roboto Mono', monospace;
        margin-bottom: 5px;
    }

    .stat-label {
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .story-visual {
        background: var(--card-bg);
        border-radius: 20px;
        padding: 30px;
        border: 1px solid var(--border);
        backdrop-filter: blur(10px);
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .visual-graphic {
        width: 100%;
        height: 300px;
        background: linear-gradient(135deg, rgba(255, 107, 53, 0.1) 0%, rgba(255, 107, 53, 0.05) 100%);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .graphic-element {
        width: 80%;
        height: 80%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 100"><path d="M10,50 Q50,10 90,50 T170,50" stroke="%23ff6b35" stroke-width="2" fill="none" opacity="0.3"/><circle cx="10" cy="50" r="3" fill="%23ff6b35"/><circle cx="90" cy="50" r="3" fill="%23ff6b35"/><circle cx="170" cy="50" r="3" fill="%23ff6b35"/></svg>') no-repeat center;
        background-size: contain;
    }

    /* Mission Section */
    .mission-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }

    .mission-card {
        background: var(--card-bg);
        border-radius: 15px;
        padding: 40px 30px;
        text-align: center;
        transition: transform 0.3s;
        border: 1px solid var(--border);
        backdrop-filter: blur(10px);
    }

    .mission-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        border-color: rgba(255, 107, 53, 0.3);
    }

    .mission-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255, 107, 53, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 32px;
        color: var(--accent);
    }

    .mission-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .mission-description {
        color: var(--text-muted);
    }

    /* Team Section */
    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
    }

    .team-member {
        background: var(--card-bg);
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        transition: transform 0.3s;
        border: 1px solid var(--border);
        backdrop-filter: blur(10px);
        position: relative;
        overflow: hidden;
    }

    .team-member:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .team-member::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: var(--gradient);
    }

    .member-image {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        margin: 0 auto 20px;
        background: var(--gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
        position: relative;
        overflow: hidden;
    }

    .member-image::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 50%;
    }

    .member-name {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .member-role {
        color: var(--accent);
        margin-bottom: 15px;
        font-weight: 500;
    }

    .member-desc {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-bottom: 20px;
    }

    .member-social {
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .social-link {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        transition: all 0.3s;
    }

    .social-link:hover {
        background: var(--accent);
        color: white;
        transform: translateY(-3px);
    }

    /* Timeline Section */
    .timeline {
        position: relative;
        max-width: 800px;
        margin: 40px auto;
    }

    .timeline::after {
        content: '';
        position: absolute;
        width: 4px;
        background-color: var(--accent);
        top: 0;
        bottom: 0;
        left: 50%;
        margin-left: -2px;
    }

    .timeline-item {
        position: relative;
        width: 50%;
        margin-bottom: 40px;
    }

    .timeline-item:nth-child(odd) {
        left: 0;
        padding-right: 40px;
    }

    .timeline-item:nth-child(even) {
        left: 50%;
        padding-left: 40px;
    }

    .timeline-content {
        background: var(--card-bg);
        border-radius: 15px;
        padding: 25px;
        border: 1px solid var(--border);
        position: relative;
    }

    .timeline-content::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        background: var(--accent);
        border-radius: 50%;
        top: 30px;
    }

    .timeline-item:nth-child(odd) .timeline-content::after {
        right: -10px;
    }

    .timeline-item:nth-child(even) .timeline-content::after {
        left: -10px;
    }

    .timeline-date {
        font-weight: 600;
        color: var(--accent);
        margin-bottom: 10px;
        font-family: 'Roboto Mono', monospace;
    }

    .timeline-desc {
        color: var(--text-muted);
    }
    /* Responsive Styles */
    @media (max-width: 968px) {
        .page-hero h1 {
            font-size: 2.8rem;
        }

        .story-grid {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .story-stats {
            justify-content: center;
        }

        .timeline::after {
            left: 31px;
        }
        
        .timeline-item {
            width: 100%;
            padding-left: 70px;
            padding-right: 0;
        }
        
        .timeline-item:nth-child(even) {
            left: 0;
        }

        .timeline-item:nth-child(odd) .timeline-content::after {
            left: -10px;
            right: auto;
        }
    }

    @media (max-width: 768px) {
        .page-hero h1 {
            font-size: 2.2rem;
        }

        .story-stats {
            flex-direction: column;
            gap: 15px;
        }

        .stat-box {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .page-hero h1 {
            font-size: 1.8rem;
        }

        .mission-card {
            padding: 30px 20px;
        }
    }
</style>

<main>
    <!-- Hero Section -->
    <section class="page-hero">
        <div class="container">
            <h1>About <span>Trade Author</span></h1>
            <p>We're building the future of cryptocurrency and stock trading with innovative tools and unparalleled security.</p>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="content-section">
        <div class="container content-container">
            <div class="section-header">
                <h2 class="section-title">Our <span>Story</span></h2>
                <p class="section-subtitle">From a simple vision to a global trading platform trusted by thousands</p>
            </div>

            <div class="story-grid">
                <div class="story-content">
                    <h3>Revolutionizing Trading Since 2018</h3>
                    <p>Founded in 2018, Trade Author emerged from a simple vision: to make professional-grade trading tools accessible to everyone. Our team of financial experts, technologists, and security specialists came together with a mission to democratize trading.</p>
                    <p>What started as a small startup has grown into a global platform serving over 500,000 traders across 120+ countries. We've processed over $4.2 billion in daily trading volume, earning the trust of both novice and professional traders.</p>
                    
                    <div class="story-stats">
                        <div class="stat-box">
                            <div class="stat-value">500K+</div>
                            <div class="stat-label">Active Traders</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-value">$4.2B+</div>
                            <div class="stat-label">Daily Volume</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-value">120+</div>
                            <div class="stat-label">Countries</div>
                        </div>
                    </div>
                </div>
                
                <div class="story-visual">
                    <div class="visual-graphic">
                        <div class="graphic-element"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Mission Section -->
    <section class="content-section dark">
        <div class="container content-container">
            <div class="section-header">
                <h2 class="section-title">Our <span>Mission</span> & Values</h2>
                <p class="section-subtitle">What drives us to innovate and excel in the trading industry</p>
            </div>

            <div class="mission-cards">
                <div class="mission-card">
                    <div class="mission-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3 class="mission-title">Our Mission</h3>
                    <p class="mission-description">To empower traders of all experience levels with the tools, data, and security needed to succeed in today's fast-paced financial markets. We believe that everyone should have access to the same advanced trading capabilities previously available only to institutional investors.</p>
                </div>

                <div class="mission-card">
                    <div class="mission-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3 class="mission-title">Our Vision</h3>
                    <p class="mission-description">To create a world where financial markets are accessible, transparent, and equitable for all. We envision a future where anyone, regardless of their background or resources, can participate confidently in global markets.</p>
                </div>

                <div class="mission-card">
                    <div class="mission-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="mission-title">Our Values</h3>
                    <p class="mission-description">We're committed to transparency, innovation, and putting our users first in everything we do. Security, integrity, and customer success form the foundation of our company culture and guide our decisions.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Team Section -->
    <section class="content-section">
        <div class="container content-container">
            <div class="section-header">
                <h2 class="section-title">Meet Our <span>Team</span></h2>
                <p class="section-subtitle">The brilliant minds behind Trade Author's success</p>
            </div>

            <div class="team-grid">
                <div class="team-member">
                    <div class="member-image">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 class="member-name">Michael Chen</h3>
                    <div class="member-role">CEO & Founder</div>
                    <p class="member-desc">Former Wall Street trader with 15+ years of experience in financial markets. Michael founded Trade Author to democratize access to professional trading tools.</p>
                    <div class="member-social">
                        <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-image">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 class="member-name">Sarah Johnson</h3>
                    <div class="member-role">Chief Technology Officer</div>
                    <p class="member-desc">Ex-Google engineer specializing in secure, scalable financial systems. Sarah leads our technology vision and platform development.</p>
                    <div class="member-social">
                        <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-github"></i></a>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-image">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 class="member-name">David Martinez</h3>
                    <div class="member-role">Chief Security Officer</div>
                    <p class="member-desc">Cybersecurity expert with background in blockchain and encryption technologies. David ensures our platform meets the highest security standards.</p>
                    <div class="member-social">
                        <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-image">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 class="member-name">Emily Wong</h3>
                    <div class="member-role">Head of Product</div>
                    <p class="member-desc">Product strategist focused on creating intuitive trading experiences. Emily translates complex financial concepts into user-friendly features.</p>
                    <div class="member-social">
                        <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline Section -->
    <section class="content-section dark">
        <div class="container content-container">
            <div class="section-header">
                <h2 class="section-title">Our <span>Journey</span></h2>
                <p class="section-subtitle">Key milestones in Trade Author's growth and development</p>
            </div>

            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="timeline-date">2018</div>
                        <p class="timeline-desc">Trade Author founded with a vision to democratize trading tools. Initial team of 5 people working from a small office.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="timeline-date">2019</div>
                        <p class="timeline-desc">Launched our first trading platform with crypto and stock support. Reached 10,000 users within 6 months.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="timeline-date">2020</div>
                        <p class="timeline-desc">Reached 100,000 active users and $1B in daily trading volume. Expanded team to 50 employees.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="timeline-date">2021</div>
                        <p class="timeline-desc">Expanded to 50+ countries and introduced mobile apps for iOS and Android. Secured Series B funding.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="timeline-date">2022</div>
                        <p class="timeline-desc">Launched advanced charting tools and API for developers. Introduced AI-powered trading insights.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="timeline-date">2023</div>
                        <p class="timeline-desc">Reached 500,000+ users and expanded to 120+ countries. Recognized as a top trading platform by industry awards.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">Join Our Trading Revolution</h2>
            <p class="cta-subtitle">Become part of the Trade Author community and experience the future of trading today.</p>

            <div class="cta-buttons">
                <a href="login.html" class="btn btn-primary">Start Trading Now</a>
                <a href="contact.html" class="btn btn-outline">Contact Our Team</a>
            </div>
        </div>
    </section>
</main>

<script>
    // Simple animation for elements
    document.addEventListener("DOMContentLoaded", function () {
        const missionCards = document.querySelectorAll(".mission-card");
        const teamMembers = document.querySelectorAll(".team-member");
        const timelineItems = document.querySelectorAll(".timeline-content");

        // Animate elements on scroll
        function animateOnScroll() {
            missionCards.forEach((card) => {
                const position = card.getBoundingClientRect().top;
                const screenPosition = window.innerHeight / 1.3;

                if (position < screenPosition) {
                    card.style.opacity = 1;
                    card.style.transform = "translateY(0)";
                }
            });

            teamMembers.forEach((member) => {
                const position = member.getBoundingClientRect().top;
                const screenPosition = window.innerHeight / 1.3;

                if (position < screenPosition) {
                    member.style.opacity = 1;
                    member.style.transform = "translateY(0)";
                }
            });

            timelineItems.forEach((item) => {
                const position = item.getBoundingClientRect().top;
                const screenPosition = window.innerHeight / 1.3;

                if (position < screenPosition) {
                    item.style.opacity = 1;
                    item.style.transform = "translateX(0)";
                }
            });
        }

        // Initialize animation styles
        missionCards.forEach((card) => {
            card.style.opacity = 0;
            card.style.transform = "translateY(20px)";
            card.style.transition = "opacity 0.5s ease, transform 0.5s ease";
        });

        teamMembers.forEach((member) => {
            member.style.opacity = 0;
            member.style.transform = "translateY(20px)";
            member.style.transition = "opacity 0.5s ease, transform 0.5s ease";
        });

        timelineItems.forEach((item) => {
            item.style.opacity = 0;
            item.style.transform = "translateX(20px)";
            item.style.transition = "opacity 0.5s ease, transform 0.5s ease";
        });

        // Listen for scroll events
        window.addEventListener("scroll", animateOnScroll);
        // Initial check
        animateOnScroll();
    });
</script>
<!-- Footer -->
<?php include 'footer.php'; ?>