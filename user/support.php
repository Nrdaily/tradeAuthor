<?php require_once '../app/config/language.php'; ?>
<?php
// support.php
require_once '../app/config/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-i18n="support_title">Trade Author - Support & Help</title>
    <?php include 'favicon.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .support-hero {
            background: var(--gradient);
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 40px;
            border-radius: 0 0 20px 20px;
        }
        
        .support-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .support-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .support-card:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(255, 107, 53, 0.15);
        }
        
        .support-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: var(--primary);
        }
        
        .faq-section {
            margin-bottom: 50px;
        }
        
        .faq-item {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 15px;
            overflow: hidden;
        }
        
        .faq-question {
            padding: 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .faq-question:hover {
            background: rgba(255, 107, 53, 0.05);
        }
        
        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s;
            color: var(--text-secondary);
        }
        
        .faq-answer.show {
            padding: 0 20px 20px 20px;
            max-height: 500px;
        }
        
        .contact-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .contact-method {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .contact-method:hover {
            border-color: var(--primary);
        }
        
        .contact-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }
        
        .quick-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.3s;
        }
        
        .quick-link:hover {
            border-color: var(--primary);
            transform: translateX(5px);
        }
        
        .status-indicators {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .status-indicator {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }
        
        .status-online {
            color: var(--success);
        }
        
        .status-offline {
            color: var(--danger);
        }
        
        .chat-bubble {
            background: var(--primary);
            color: white;
            padding: 15px 20px;
            border-radius: 20px;
            margin: 20px 0;
            display: inline-block;
            max-width: 80%;
        }
    </style>
</head>
<body class="theme-transition">
    <?php include "nav.php"; ?>

    <div class="main-content">
        <div class="container">
            <!-- Support Hero -->
            <div class="support-hero">
                <div class="container" style="max-width: 800px;">
                    <h1 style="margin-bottom: 15px;" data-i18n="support_heading">How can we help you?</h1>
                    <p style="font-size: 1.2rem; margin-bottom: 30px; opacity: 0.9;" data-i18n="support_subheading">
                        Get instant help and support for all your trading needs
                    </p>
                    
                    <div style="max-width: 500px; margin: 0 auto;">
                        <div style="position: relative;">
                            <input type="text" placeholder="Search for help..." style="width: 100%; padding: 15px 50px 15px 20px; border: none; border-radius: 25px; background: rgba(255,255,255,0.15); color: white; font-size: 1rem;" data-i18n-placeholder="search_help">
                            <i class="fas fa-search" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support Options Grid -->
            <div class="support-grid">
                <div class="support-card" onclick="showSection('faq')">
                    <div class="support-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h3 data-i18n="faq">FAQ</h3>
                    <p data-i18n="faq_desc">Find answers to frequently asked questions</p>
                </div>
                
                <div class="support-card" onclick="showSection('contact')">
                    <div class="support-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 data-i18n="contact_support">Contact Support</h3>
                    <p data-i18n="contact_support_desc">Get in touch with our support team</p>
                </div>
                
                <div class="support-card" onclick="showSection('guides')">
                    <div class="support-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h3 data-i18n="trading_guides">Trading Guides</h3>
                    <p data-i18n="trading_guides_desc">Learn how to trade effectively</p>
                </div>
                
                <div class="support-card" onclick="showSection('status')">
                    <div class="support-icon">
                        <i class="fas fa-server"></i>
                    </div>
                    <h3 data-i18n="system_status">System Status</h3>
                    <p data-i18n="system_status_desc">Check platform status and maintenance</p>
                </div>
            </div>

            <!-- FAQ Section -->
            <div id="faq-section" class="faq-section">
                <h2 style="margin-bottom: 25px;" data-i18n="frequently_asked_questions">Frequently Asked Questions</h2>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(1)">
                        <span data-i18n="faq1_question">How do I buy cryptocurrency?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer" id="faq1-answer">
                        <p data-i18n="faq1_answer">To buy cryptocurrency, navigate to the Buy page, select your preferred cryptocurrency, enter the amount you want to purchase, and complete the payment process using your preferred payment method.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(2)">
                        <span data-i18n="faq2_question">What are the trading fees?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer" id="faq2-answer">
                        <p data-i18n="faq2_answer">Our trading fees are competitive and transparent. For most trades, we charge a 0.1% taker fee and 0.2% maker fee. Fees may vary based on your trading volume and membership tier.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(3)">
                        <span data-i18n="faq3_question">How do I secure my account?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer" id="faq3-answer">
                        <p data-i18n="faq3_answer">We recommend enabling two-factor authentication, using a strong unique password, and regularly monitoring your account activity. Never share your login credentials with anyone.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(4)">
                        <span data-i18n="faq4_question">What is the minimum deposit amount?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer" id="faq4-answer">
                        <p data-i18n="faq4_answer">The minimum deposit amount varies by payment method. For credit card purchases, the minimum is $10. For bank transfers, the minimum is $50.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(5)">
                        <span data-i18n="faq5_question">How long do withdrawals take?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer" id="faq5-answer">
                        <p data-i18n="faq5_answer">Withdrawal processing times vary by cryptocurrency and network conditions. Most withdrawals are processed within 30 minutes, but during high network congestion, it may take longer.</p>
                    </div>
                </div>
            </div>

            <!-- Contact Methods -->
            <div id="contact-section" class="faq-section">
                <h2 style="margin-bottom: 25px;" data-i18n="contact_support">Contact Support</h2>
                
                <div class="contact-methods">
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3 data-i18n="email_support">Email Support</h3>
                        <p data-i18n="email_support_desc">Get help via email</p>
                        <a href="mailto:support@tradeauthor.com" class="btn btn-primary" style="margin-top: 15px;" data-i18n="send_email">
                            Send Email
                        </a>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fab fa-telegram"></i>
                        </div>
                        <h3 data-i18n="telegram_support">Telegram Support</h3>
                        <p data-i18n="telegram_support_desc">Instant messaging support</p>
                        <button class="btn btn-primary" style="margin-top: 15px;" onclick="openTelegram()" data-i18n="join_telegram">
                            Join Telegram
                        </button>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h3 data-i18n="phone_support">Phone Support</h3>
                        <p data-i18n="phone_support_desc">Speak with our team</p>
                        <a href="tel:+1-555-123-4567" class="btn btn-primary" style="margin-top: 15px;" data-i18n="call_now">
                            Call Now
                        </a>
                    </div>
                </div>
                
                <div class="card" style="margin-top: 30px;">
                    <h3 style="margin-bottom: 20px;" data-i18n="live_chat">Live Chat Support</h3>
                    <p data-i18n="live_chat_desc">Chat with our support team in real-time</p>
                    
                    <div style="background: var(--background); border-radius: 10px; padding: 20px; margin: 20px 0;">
                        <div class="chat-bubble" style="background: var(--border); color: var(--text-primary);">
                            <span data-i18n="chat_welcome">Hi! How can we help you today?</span>
                        </div>
                        
                        <div style="display: flex; gap: 10px;">
                            <input type="text" placeholder="Type your message..." style="flex: 1; padding: 12px 15px; border: 1px solid var(--border); border-radius: 20px; background: var(--card-bg); color: var(--text-primary);" data-i18n-placeholder="type_message">
                            <button class="btn btn-primary" style="border-radius: 20px; padding: 12px 20px;" data-i18n="send">
                                Send
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="quick-links">
                <a href="../assets/docs/user-guide.pdf" class="quick-link" target="_blank">
                    <i class="fas fa-file-pdf" style="color: var(--danger);"></i>
                    <span data-i18n="user_guide">User Guide (PDF)</span>
                </a>
                <a href="../assets/docs/security-best-practices.pdf" class="quick-link" target="_blank">
                    <i class="fas fa-shield-alt" style="color: var(--primary);"></i>
                    <span data-i18n="security_guide">Security Guide</span>
                </a>
                <a href="tutorials" class="quick-link">
                    <i class="fas fa-video" style="color: var(--info);"></i>
                    <span data-i18n="video_tutorials">Video Tutorials</span>
                </a>
                <a href="api-documentation" class="quick-link">
                    <i class="fas fa-code" style="color: var(--secondary);"></i>
                    <span data-i18n="api_docs">API Documentation</span>
                </a>
            </div>
        </div>
    </div>

    <script>
        // FAQ functionality
        function toggleFAQ(number) {
            const answer = document.getElementById('faq' + number + '-answer');
            const isVisible = answer.classList.contains('show');
            
            // Close all FAQs
            document.querySelectorAll('.faq-answer').forEach(faq => {
                faq.classList.remove('show');
            });
            
            // Toggle current FAQ
            if (!isVisible) {
                answer.classList.add('show');
            }
        }
        
        // Section navigation
        function showSection(section) {
            // Hide all sections
            document.querySelectorAll('.faq-section').forEach(sec => {
                sec.style.display = 'none';
            });
            
            // Show selected section
            document.getElementById(section + '-section').style.display = 'block';
            
            // Scroll to section
            document.getElementById(section + '-section').scrollIntoView({
                behavior: 'smooth'
            });
        }
        
        // Telegram support
        function openTelegram() {
            window.open('https://t.me/tradeauthor_support', '_blank');
        }
        
        // Initialize support page
        document.addEventListener('DOMContentLoaded', function() {
            // Show FAQ section by default
            showSection('faq');
        });
    </script>
</body>
</html>