<?php include 'header.php'; ?>
<title>Contact Us - Trade Author | Advanced Crypto & Stock Trading Platform</title>
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

    /* Contact Grid */
    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
    }

    .contact-info {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .contact-card {
        background: var(--card-bg);
        border-radius: 15px;
        padding: 30px;
        border: 1px solid var(--border);
        backdrop-filter: blur(10px);
        transition: transform 0.3s;
    }

    .contact-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        border-color: rgba(255, 107, 53, 0.3);
    }

    .contact-card h3 {
        font-size: 1.5rem;
        margin-bottom: 20px;
        color: var(--accent);
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 20px;
    }

    .info-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: rgba(255, 107, 53, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent);
        flex-shrink: 0;
        font-size: 1.2rem;
    }

    .info-content h4 {
        font-weight: 600;
        margin-bottom: 5px;
    }

    .info-content p {
        color: var(--text-muted);
    }

    .support-hours {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .hour-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .hour-day {
        font-weight: 500;
    }

    .hour-time {
        color: var(--text-muted);
    }

    /* Contact Form */
    .contact-form {
        background: var(--card-bg);
        border-radius: 15px;
        padding: 40px;
        border: 1px solid var(--border);
        backdrop-filter: blur(10px);
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .form-input {
        width: 100%;
        padding: 15px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border);
        border-radius: 10px;
        color: var(--text);
        font-size: 16px;
        transition: all 0.3s;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.2);
    }

    textarea.form-input {
        min-height: 150px;
        resize: vertical;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .priority-badge {
        display: inline-block;
        padding: 5px 12px;
        background: rgba(255, 107, 53, 0.1);
        color: var(--accent);
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-left: 10px;
    }

    /* FAQ Section */
    .faq-section {
        margin-top: 40px;
    }

    .faq-item {
        background: var(--card-bg);
        border-radius: 10px;
        margin-bottom: 15px;
        border: 1px solid var(--border);
        overflow: hidden;
        transition: all 0.3s;
    }

    .faq-item:hover {
        border-color: rgba(255, 107, 53, 0.3);
    }

    .faq-question {
        padding: 20px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.3s;
    }

    .faq-question:hover {
        background: rgba(255, 255, 255, 0.02);
    }

    .faq-answer {
        padding: 0 20px 20px;
        color: var(--text-muted);
        display: none;
        line-height: 1.7;
    }

    .faq-item.active .faq-answer {
        display: block;
    }

    .faq-item.active .faq-toggle i {
        transform: rotate(180deg);
    }

    .faq-toggle {
        transition: transform 0.3s;
        color: var(--accent);
    }

    /* Support Channels */
    .support-channels {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        margin-top: 60px;
    }

    .channel-card {
        background: var(--card-bg);
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        border: 1px solid var(--border);
        backdrop-filter: blur(10px);
        transition: transform 0.3s;
    }

    .channel-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        border-color: rgba(255, 107, 53, 0.3);
    }

    .channel-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: rgba(255, 107, 53, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 28px;
        color: var(--accent);
    }

    .channel-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .channel-desc {
        color: var(--text-muted);
        margin-bottom: 20px;
    }

    .channel-response {
        display: inline-block;
        padding: 5px 15px;
        background: rgba(0, 200, 83, 0.1);
        color: var(--success);
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Responsive Styles */
    @media (max-width: 968px) {
        .page-hero h1 {
            font-size: 2.8rem;
        }

        .contact-grid {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .form-row {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .page-hero h1 {
            font-size: 2.2rem;
        }

        .contact-form {
            padding: 30px 20px;
        }
    }

    @media (max-width: 480px) {
        .page-hero h1 {
            font-size: 1.8rem;
        }

        .contact-card,
        .channel-card {
            padding: 20px;
        }
    }
</style>

<main>
    <!-- Hero Section -->
    <section class="page-hero">
        <div class="container">
            <h1>Contact <span>Trade Author</span></h1>
            <p>Have questions or need assistance? Our team is here to help you with any inquiries.</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="content-section">
        <div class="container content-container">
            <div class="section-header">
                <h2 class="section-title">Get In <span>Touch</span></h2>
                <p class="section-subtitle">We're here to help you with any questions about our platform, features, or trading services.</p>
            </div>

            <div class="contact-grid">
                <div class="contact-info">
                    <div class="contact-card">
                        <h3>Contact Information</h3>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info-content">
                                <h4>Email</h4>
                                <p>support@tradeauthor.com</p>
                            </div>
                        </div>

                        <!-- <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="info-content">
                                <h4>Phone</h4>
                                <p>+1 (555) 123-4567</p>
                            </div>
                        </div> -->

                        <!-- <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="info-content">
                                <h4>Address</h4>
                                <p>123 Financial District, New York, NY 10004, United States</p>
                            </div>
                        </div> -->
                    </div>

                    <div class="contact-card">
                        <h3>Support Hours</h3>
                        <div class="support-hours">
                            <div class="hour-item">
                                <span class="hour-day">Monday - Friday</span>
                                <span class="hour-time">24/7</span>
                            </div>
                            <div class="hour-item">
                                <span class="hour-day">Saturday - Sunday</span>
                                <span class="hour-time">24/7</span>
                            </div>
                            <div class="hour-item">
                                <span class="hour-day">Emergency Support</span>
                                <span class="hour-time">Always Available</span>
                            </div>
                        </div>
                        <div style="margin-top: 20px; padding: 10px; background: rgba(0, 200, 83, 0.1); border-radius: 8px;">
                            <p style="color: var(--success); font-size: 0.9rem; text-align: center;">
                                <i class="fas fa-clock"></i> Average response time: <strong>Under 1 hour</strong>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="contact-form">
                    <h3>Send us a Message</h3>
                    <form id="contactForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name" class="form-label">Full Name <span class="priority-badge">Required</span></label>
                                <input type="text" id="name" class="form-input" required>
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Email Address <span class="priority-badge">Required</span></label>
                                <input type="email" id="email" class="form-input" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject" class="form-label">Subject <span class="priority-badge">Required</span></label>
                            <input type="text" id="subject" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label for="category" class="form-label">Category</label>
                            <select id="category" class="form-input">
                                <option value="">Select a category</option>
                                <option value="technical">Technical Support</option>
                                <option value="account">Account Issues</option>
                                <option value="trading">Trading Questions</option>
                                <option value="billing">Billing & Payments</option>
                                <option value="security">Security Concerns</option>
                                <option value="general">General Inquiry</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="message" class="form-label">Message <span class="priority-badge">Required</span></label>
                            <textarea id="message" class="form-input" required></textarea>
                        </div>

                        <div class="form-group">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" id="urgent" style="width: auto;">
                                <label for="urgent" style="margin: 0; font-weight: 500;">Mark as urgent <span class="priority-badge">Priority</span></label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>

            <!-- Support Channels -->
            <div class="support-channels">
                <div class="channel-card">
                    <div class="channel-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3 class="channel-title">Live Chat</h3>
                    <p class="channel-desc">Get instant help from our support team with our live chat feature.</p>
                    <div class="channel-response">Response: Instant</div>
                </div>

                <div class="channel-card">
                    <div class="channel-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="channel-title">Phone Support</h3>
                    <p class="channel-desc">Speak directly with our support specialists for complex issues.</p>
                    <div class="channel-response">Response: Immediate</div>
                </div>

                <div class="channel-card">
                    <div class="channel-icon">
                        <i class="fab fa-discord"></i>
                    </div>
                    <h3 class="channel-title">Community Discord</h3>
                    <p class="channel-desc">Join our community of traders for tips, strategies, and peer support.</p>
                    <div class="channel-response">Response: Community-based</div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="faq-section">
                <div class="section-header">
                    <h2 class="section-title">Frequently Asked <span>Questions</span></h2>
                    <p class="section-subtitle">Quick answers to common questions about our platform and services.</p>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        How do I create an account on Trade Author?
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <p>Creating an account is simple. Click on the 'Sign Up' button on our homepage, provide your email address, create a secure password, and complete the verification process. You'll be ready to start trading in minutes.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        What security measures do you have in place?
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <p>We employ bank-level security including two-factor authentication (2FA), SSL encryption, cold storage for digital assets, and regular security audits. Your funds and data are protected with industry-leading security practices.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        What trading options are available?
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <p>Trade Author offers trading in cryptocurrencies, stocks, ETFs, forex, and commodities. Our platform supports spot trading, margin trading, and futures contracts with advanced charting tools and real-time market data.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        How do deposits and withdrawals work?
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <p>You can deposit funds via bank transfer, credit/debit card, or cryptocurrency transfer. Withdrawals are processed within 24 hours. Specific processing times may vary depending on your payment method and verification status.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        Do you have mobile apps?
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <p>Yes, we have fully-featured mobile apps for both iOS and Android devices. You can download them from the App Store or Google Play Store. Our mobile apps offer the same functionality as our web platform.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">Ready to Start Trading?</h2>
            <p class="cta-subtitle">Join thousands of traders who have already chosen Trade Author for their trading journey.</p>

            <div class="cta-buttons">
                <a href="login" class="btn btn-primary">Create Free Account</a>
                <a href="markets" class="btn btn-outline">Explore Markets</a>
            </div>
        </div>
    </section>
</main>

<script>
    // FAQ toggle functionality
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', () => {
            const item = question.parentElement;
            item.classList.toggle('active');
        });
    });


    // Simple animation for elements
    document.addEventListener("DOMContentLoaded", function() {
        const contactCards = document.querySelectorAll(".contact-card");
        const channelCards = document.querySelectorAll(".channel-card");
        const faqItems = document.querySelectorAll(".faq-item");

        // Animate elements on scroll
        function animateOnScroll() {
            contactCards.forEach((card) => {
                const position = card.getBoundingClientRect().top;
                const screenPosition = window.innerHeight / 1.3;

                if (position < screenPosition) {
                    card.style.opacity = 1;
                    card.style.transform = "translateY(0)";
                }
            });

            channelCards.forEach((card) => {
                const position = card.getBoundingClientRect().top;
                const screenPosition = window.innerHeight / 1.3;

                if (position < screenPosition) {
                    card.style.opacity = 1;
                    card.style.transform = "translateY(0)";
                }
            });

            faqItems.forEach((item) => {
                const position = item.getBoundingClientRect().top;
                const screenPosition = window.innerHeight / 1.3;

                if (position < screenPosition) {
                    item.style.opacity = 1;
                    item.style.transform = "translateY(0)";
                }
            });
        }

        // Initialize animation styles
        contactCards.forEach((card) => {
            card.style.opacity = 0;
            card.style.transform = "translateY(20px)";
            card.style.transition = "opacity 0.5s ease, transform 0.5s ease";
        });

        channelCards.forEach((card) => {
            card.style.opacity = 0;
            card.style.transform = "translateY(20px)";
            card.style.transition = "opacity 0.5s ease, transform 0.5s ease";
        });

        faqItems.forEach((item) => {
            item.style.opacity = 0;
            item.style.transform = "translateY(10px)";
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