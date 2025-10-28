<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta charset="UTF-8" />
  <meta
    name="description"
    content="Trade cryptocurrencies and stocks with advanced tools, real-time data, and enterprise security. Professional trading platform for all experience levels." />
  <meta
    name="keywords"
    content="crypto trading, stock trading, cryptocurrency exchange, bitcoin trading, stock market, investment platform, blockchain, trading platform" />
  <meta name="author" content="Trade Author Financial Technologies" />
  <meta name="robots" content="index, follow" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://tradeauthor.com/" />
  <meta
    property="og:title"
    content="Trade Author | Advanced Crypto & Stock Trading Platform" />
  <meta
    property="og:description"
    content="Trade cryptocurrencies and stocks with advanced tools, real-time data, and enterprise security." />
  <meta property="og:image" content="https://tradeauthor.com/assets/icons/favicon.png" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <meta property="og:locale" content="en_US" />
  <meta property="twitter:card" content="summary_large_image" />
  <meta property="twitter:url" content="https://tradeauthor.com/" />
  <meta
    property="twitter:title"
    content="Trade Author | Advanced Crypto & Stock Trading Platform" />
  <meta
    property="twitter:description"
    content="Trade cryptocurrencies and stocks with advanced tools, real-time data, and enterprise security." />
  <link rel="canonical" href="https://tradeauthor.com" />
  <meta name="theme-color" content="#2a160fff" />
  <meta name="msapplication-TileColor" content="#2a170fff" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
  <meta name="apple-mobile-web-app-title" content="Trade Author" />
  <meta name="application-name" content="Trade Author" />
  <meta name="format-detection" content="telephone=no" />
  <link rel="icon" type="image/x-icon" href="assets/icons/favicon.png" />
  <link rel="icon" type="image/png" sizes="16x16" href="assets/icons/favicon.png" />
  <link rel="icon" type="image/png" sizes="32x32" href="assets/icons/favicon.png" />
  <link
    rel="icon"
    type="image/png"
    sizes="192x192"
    href="/android-chrome-192x192.png" />
  <link
    rel="icon"
    type="image/png"
    sizes="512x512"
    href="/android-chrome-512x512.png" />
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
  <link rel="manifest" href="/site.webmanifest" />

  <title>Trade Author | Advanced Crypto & Stock Trading Platform</title>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
    rel="stylesheet" />
  <!-- Add these before the closing </body> tag -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="realtime-crypto.js"></script>
  <script src="trading-chart.js"></script>
  <script src="realtime-markets.js"></script>
  <!-- <link rel="stylesheet" href="assets/css/style.css"> -->
  <style>
    :root {
      --primary: #0a0a0a;
      --secondary: #1a1a1a;
      --accent: #ff6b35;
      --accent-dark: #e55a2b;
      --text: #f5f5f5;
      --text-muted: #b0b0b0;
      --success: #00c853;
      --danger: #ff3d00;
      --warning: #ffab00;
      --border: #333333;
      --card-bg: rgba(30, 30, 30, 0.7);
      --gradient: linear-gradient(135deg, #ff6b35 0%, #ff8e53 100%);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      text-decoration: none;
    }

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    body {
      font-family: "Inter", sans-serif;
      background-color: var(--primary);
      color: var(--text);
      line-height: 1.6;
      overflow-x: hidden;
    }

    /* Header Styles */
    header {
      background-color: rgba(10, 10, 10, 0.9);
      backdrop-filter: blur(10px);
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
      border-bottom: 1px solid var(--border);
    }

    .header-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .logo-icon {
      width: 150px;
      height: 40px;
      border-radius: 8px;
      /* background: var(--gradient); */
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      font-size: 18px;
    }

    .logo-text {
      font-size: 24px;
      font-weight: 700;
      font-family: 'Roboto Mono', monospace;
    }

    .logo-text span {
      color: var(--accent);
    }

    nav ul {
      display: flex;
      list-style: none;
      gap: 30px;
    }

    nav a {
      color: var(--text);
      font-weight: 500;
      transition: color 0.3s;
      position: relative;
      padding: 5px 0;
    }

    nav a:hover {
      color: var(--accent);
    }

    nav a.active {
      color: var(--accent);
    }

    nav a::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 0;
      height: 2px;
      background: var(--gradient);
      transition: width 0.3s;
    }

    nav a:hover::after,
    nav a.active::after {
      width: 100%;
    }

    .auth-buttons {
      display: flex;
      gap: 15px;
    }

    .btn {
      padding: 10px 20px;
      border-radius: 30px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      border: none;
      font-size: 14px;
    }

    .btn-outline {
      background: transparent;
      color: var(--text);
      border: 1px solid var(--border);
    }

    .btn-outline:hover {
      background: rgba(255, 107, 53, 0.1);
      border-color: var(--accent);
    }

    .btn-primary {
      background: var(--gradient);
      color: white;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
    }

    /* Mobile Navigation */
    .mobile-nav-toggle {
      display: none;
      background: transparent;
      border: none;
      color: var(--text);
      font-size: 1.5rem;
      cursor: pointer;
      z-index: 1000;
    }

    .mobile-nav {
      position: fixed;
      top: 0;
      left: -100%;
      width: 80%;
      max-width: 300px;
      height: 100vh;
      background: var(--secondary);
      padding: 80px 30px 30px;
      transition: left 0.3s ease-in-out;
      z-index: 999;
      box-shadow: -5px 0 25px rgba(0, 0, 0, 0.5);
      overflow-y: auto;
    }

    .mobile-nav.active {
      left: 0;
    }

    .mobile-nav ul {
      list-style: none;
    }

    .mobile-nav li {
      margin-bottom: 15px;
    }

    .mobile-nav a {
      color: var(--text);
      text-decoration: none;
      font-size: 1.2rem;
      font-weight: 500;
      display: block;
      padding: 12px 15px;
      border-radius: 8px;
      transition: all 0.3s;
    }

    .mobile-nav a:hover,
    .mobile-nav a.active {
      background: rgba(255, 107, 53, 0.1);
      color: var(--accent);
    }

    .mobile-nav-auth {
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid var(--border);
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.7);
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease-in-out;
      z-index: 998;
    }

    .overlay.active {
      opacity: 1;
      visibility: visible;
    }



    /* Content Sections */
    .content-section {
      padding: 100px 0;
    }

    .content-section.dark {
      background: var(--secondary);
    }

    .content-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }

    /* Sections */
    .section {
      padding: 60px 0;
    }


    .section-header {
      text-align: center;
      margin-bottom: 60px;
    }

    .section-title {
      font-size: 2.5rem;
      font-weight: 700;
      text-align: center;
      margin-bottom: 20px;
    }

    .section-title span {
      color: var(--accent);
    }

    .section-subtitle {
      text-align: center;
      color: var(--text-muted);
      max-width: 600px;
      margin: 0 auto 60px;
      font-size: 1.1rem;
    }

    /* CTA Section */
    .cta {
      text-align: center;
      background: radial-gradient(circle at 50% 50%, rgba(255, 107, 53, 0.1) 0%, transparent 70%);
    }

    .cta-title {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 20px;
    }

    .cta-subtitle {
      color: var(--text-muted);
      max-width: 600px;
      margin: 0 auto 40px;
      font-size: 1.1rem;
    }

    .cta-buttons {
      display: flex;
      gap: 15px;
      justify-content: center;
    }

    /* Footer */
    footer {
      background: var(--secondary);
      padding: 60px;
      border-top: 1px solid var(--border);
    }

    .footer-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 40px;
      margin-bottom: 30px;
    }

    .footer-col h4 {
      font-size: 1.2rem;
      margin-bottom: 20px;
      font-weight: 600;
    }

    .footer-links {
      list-style: none;
    }

    .footer-links li {
      margin-bottom: 10px;
    }

    .footer-links a {
      color: var(--text-muted);
      transition: color 0.3s;
    }

    .footer-links a:hover {
      color: var(--accent);
    }

    .footer-bottom {
      text-align: center;
      padding-top: 30px;
      border-top: 1px solid var(--border);
      color: var(--text-muted);
      font-size: 0.9rem;
    }


    /* Responsive Styles */
    @media (max-width: 968px) {

      nav ul,
      .auth-buttons {
        display: none;
      }

      .mobile-nav-toggle {
        display: block;
      }

      .cta-buttons {
        flex-direction: column;
        align-items: center;
      }
    }

    @media (max-width: 768px) {
      .hero-title {
        font-size: 2.2rem;
      }

      .section-title {
        font-size: 2rem;
      }

      .markets-tabs {
        flex-wrap: wrap;
      }

      .cta-title {
        font-size: 2rem;
      }
    }

    @media (max-width: 768px) {
      .hero-title {
        font-size: 2.2rem;
      }

      .section-title {
        font-size: 2rem;
      }

      .cta-title {
        font-size: 2rem;
      }
    }

    @media (max-width: 480px) {
      .header-container {
        padding: 20px 15px;
      }

      .auth-buttons {
        display: none;
      }

      .logo-text {
        font-size: 20px;
      }

      .mobile-nav {
        width: 85%;
      }
    }

    /* Responsive Styles */
    @media (max-width: 968px) {
      .desktop-nav {
        display: none;
      }

      .auth-buttons {
        display: none;
      }

      .mobile-nav-toggle {
        display: block;
      }
    }
  </style>
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FinancialService",
      "name": "Trade Author",
      "url": "https://tradeauthor.com",
      "logo": "https://tradeauthor.com/assets/icons/favicon.png",
      "description": "Professional trading platform for cryptocurrencies and stocks with enterprise security and advanced trading tools.",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "123 Financial District",
        "addressLocality": "New York",
        "addressRegion": "NY",
        "postalCode": "10004",
        "addressCountry": "US"
      }
    }
  </script>

</head>

<body>
  <!-- Mobile Navigation -->
  <div class="mobile-nav">
    <ul>
      <li><a href="index" class="active"><i class="fas fa-home"></i> Home</a></li>
      <li><a href="markets"><i class="fas fa-chart-bar"></i> Markets</a></li>
      <li><a href="about"><i class="fas fa-info-circle"></i> About</a></li>
      <li><a href="contact"><i class="fas fa-envelope"></i> Contact</a></li>
      <li><a href="terms"><i class="fas fa-file-alt"></i> Terms</a></li>
      <li><a href="privacy"><i class="fas fa-shield-alt"></i> Privacy</a></li>
    </ul>

    <div class="mobile-nav-auth">
      <a href="login" class="btn btn-outline" style="text-align: center;">Log In</a>
      <a href="login" class="btn btn-primary" style="text-align: center;">Sign Up</a>
    </div>
  </div>
  <div class="overlay"></div>

  <!-- Header -->
  <header>
    <div class="container header-container">
      <a href="index" class="logo">
        <div class="logo-icon">
          <img src="assets/icons/logo.png" alt="Trade Author Logo">
        </div>
      </a>

      <nav class="desktop-nav">
        <ul>
          <li><a href="index" class="active">Home</a></li>
          <li><a href="markets">Markets</a></li>
          <li><a href="about">About</a></li>
          <li><a href="contact">Contact</a></li>
        </ul>
      </nav>

      <div class="auth-buttons">
        <a href="login" class="btn btn-outline">Log In</a>
        <a href="login" class="btn btn-primary">Sign Up</a>
      </div>

      <button class="mobile-nav-toggle">
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </header>