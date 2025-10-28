<?php include 'header.php'; ?>
<!-- Privacy Page Styles -->
<style>
  .privacy-hero {
    background: linear-gradient(135deg, var(--primary) 0%, #3b2f1eff 100%);
    padding: 80px 0;
    text-align: center;
  }

  .privacy-hero-content {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 20px;
  }

  .privacy-hero h1 {
    font-size: 3rem;
    margin-bottom: 20px;
  }

  .privacy-hero p {
    font-size: 1.2rem;
    color: var(--text-muted);
    margin-bottom: 40px;
  }

  .privacy-content {
    padding: 80px 0;
    background-color: var(--primary);
  }

  .privacy-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 20px;
  }

  .privacy-section {
    margin-bottom: 50px;
  }

  .privacy-section h2 {
    font-size: 1.8rem;
    margin-bottom: 20px;
    color: var(--accent);
  }

  .privacy-section p {
    color: var(--text-muted);
    margin-bottom: 20px;
    line-height: 1.8;
  }

  .privacy-section ul {
    color: var(--text-muted);
    margin-bottom: 20px;
    padding-left: 20px;
  }

  .privacy-section li {
    margin-bottom: 10px;
    line-height: 1.6;
  }

  .privacy-section h3 {
    font-size: 1.3rem;
    margin: 25px 0 15px;
    color: var(--text);
  }

  .last-updated {
    text-align: center;
    color: var(--text-muted);
    font-style: italic;
    margin-bottom: 40px;
  }

  .back-to-top {
    display: inline-block;
    margin-top: 20px;
    color: var(--accent);
    text-decoration: none;
    font-weight: 500;
  }

  .back-to-top:hover {
    text-decoration: underline;
  }

  @media (max-width: 768px) {
    .privacy-section h2 {
      font-size: 1.5rem;
    }
  }
</style>

<!-- Privacy Hero Section -->
<section class="privacy-hero">
  <div class="privacy-hero-content">
    <h1>Privacy Policy</h1>
    <p>We are committed to protecting your privacy and personal information.</p>
  </div>
</section>

<!-- Privacy Content Section -->
<section class="privacy-content">
  <div class="privacy-container">
    <div class="last-updated">Last Updated: January 15, <?= date('Y');?>
</div>
    
    <div class="privacy-section">
      <h2>1. Introduction</h2>
      <p>Trade Author ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our Services.</p>
      <p>Please read this Privacy Policy carefully. By accessing or using our Services, you agree to the collection and use of information in accordance with this policy.</p>
    </div>
    
    <div class="privacy-section">
      <h2>2. Information We Collect</h2>
      <p>We collect several types of information from and about users of our Services, including:</p>
      
      <h3>2.1. Personal Information</h3>
      <p>When you create an account or use our Services, we may collect personal information that can be used to identify you, such as:</p>
      <ul>
        <li>Full name</li>
        <li>Email address</li>
        <li>Phone number</li>
        <li>Date of birth</li>
        <li>Government-issued identification</li>
        <li>Proof of address</li>
        <li>Financial information</li>
      </ul>
      
      <h3>2.2. Technical Information</h3>
      <p>When you use our Services, we automatically collect certain information about your device and usage, including:</p>
      <ul>
        <li>IP address</li>
        <li>Browser type and version</li>
        <li>Device information</li>
        <li>Pages visited and features used</li>
        <li>Time and date of access</li>
      </ul>
      
      <h3>2.3. Cookies and Similar Technologies</h3>
      <p>We use cookies and similar tracking technologies to track activity on our Services and store certain information. Cookies are files with a small amount of data that may include an anonymous unique identifier.</p>
    </div>
    
    <div class="privacy-section">
      <h2>3. How We Use Your Information</h2>
      <p>We use the information we collect for various purposes, including to:</p>
      <ul>
        <li>Provide, maintain, and improve our Services</li>
        <li>Process transactions and send related information</li>
        <li>Verify your identity and prevent fraud</li>
        <li>Send technical notices, updates, and security alerts</li>
        <li>Respond to your comments, questions, and requests</li>
        <li>Communicate about products, services, offers, and events</li>
        <li>Monitor and analyze trends, usage, and activities</li>
        <li>Personalize and improve our Services</li>
        <li>Comply with legal obligations</li>
      </ul>
    </div>
    
    <div class="privacy-section">
      <h2>4. How We Share Your Information</h2>
      <p>We may share your information in the following circumstances:</p>
      
      <h3>4.1. With Service Providers</h3>
      <p>We may share your information with third-party vendors, service providers, and partners who need access to such information to carry out work on our behalf, such as:</p>
      <ul>
        <li>Payment processors</li>
        <li>Identity verification services</li>
        <li>Customer support providers</li>
        <li>Analytics providers</li>
      </ul>
      
      <h3>4.2. For Legal Reasons</h3>
      <p>We may disclose your information if we believe it is necessary to:</p>
      <ul>
        <li>Comply with applicable law, regulation, or legal process</li>
        <li>Protect the rights, property, or safety of Trade Author, our users, or others</li>
        <li>Prevent fraud or abuse</li>
        <li>Enforce our terms and policies</li>
      </ul>
      
      <h3>4.3. Business Transfers</h3>
      <p>If we are involved in a merger, acquisition, or sale of all or a portion of our assets, your information may be transferred as part of that transaction.</p>
    </div>
    
    <div class="privacy-section">
      <h2>5. Data Retention</h2>
      <p>We will retain your personal information only for as long as is necessary for the purposes set out in this Privacy Policy, or as required to comply with our legal obligations, resolve disputes, and enforce our agreements.</p>
      <p>Even if you close your account, we may retain certain information as necessary to comply with our legal obligations, prevent fraud, collect any fees owed, resolve disputes, and assist with any investigations.</p>
    </div>
    
    <div class="privacy-section">
      <h2>6. Data Security</h2>
      <p>We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. These measures include:</p>
      <ul>
        <li>Encryption of data in transit and at rest</li>
        <li>Regular security assessments and testing</li>
        <li>Access controls and authentication mechanisms</li>
        <li>Physical security measures at our facilities</li>
        <li>Employee training on data protection</li>
      </ul>
      <p>However, no method of transmission over the Internet or electronic storage is 100% secure, and we cannot guarantee absolute security.</p>
    </div>
    
    <div class="privacy-section">
      <h2>7. Your Rights and Choices</h2>
      <p>Depending on your location, you may have certain rights regarding your personal information, such as:</p>
      <ul>
        <li>Accessing and receiving a copy of your personal information</li>
        <li>Correcting inaccurate or incomplete information</li>
        <li>Deleting your personal information</li>
        <li>Restricting or objecting to our processing of your information</li>
        <li>Data portability</li>
        <li>Withdrawing consent</li>
      </ul>
      <p>To exercise any of these rights, please contact us using the contact details provided below.</p>
    </div>
    
    <div class="privacy-section">
      <h2>8. International Data Transfers</h2>
      <p>Your information may be transferred to, and processed in, countries other than the country in which you reside. These countries may have data protection laws that are different from the laws of your country.</p>
      <p>Whenever we transfer your personal information outside of your country, we ensure a similar degree of protection is afforded to it by implementing appropriate safeguards.</p>
    </div>
    
    <div class="privacy-section">
      <h2>9. Children's Privacy</h2>
      <p>Our Services are not intended for children under 18 years of age. We do not knowingly collect personal information from children under 18. If you are a parent or guardian and believe we have collected information from your child, please contact us immediately.</p>
    </div>
    
    <div class="privacy-section">
      <h2>10. Changes to This Privacy Policy</h2>
      <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date.</p>
      <p>You are advised to review this Privacy Policy periodically for any changes. Changes to this Privacy Policy are effective when they are posted on this page.</p>
    </div>
    
    <div class="privacy-section">
      <h2>11. Contact Us</h2>
      <p>If you have any questions or concerns about this Privacy Policy or our data practices, please contact us at:</p>
      <p>Trade Author<br>
      123 Financial District<br>
      New York, NY 10004<br>
      United States<br>
      Email: privacy@tradeauthor.com</p>
    </div>
    
    <a href="#" class="back-to-top">Back to Top</a>
  </div>
</section>

<script>
// Back to top functionality
document.querySelector('.back-to-top').addEventListener('click', function(e) {
  e.preventDefault();
  window.scrollTo({ top: 0, behavior: 'smooth' });
});
</script>
<?php include 'footer.php'; ?>