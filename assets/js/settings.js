function initLanguageSystem() {
  const langToggle = document.getElementById("language-toggle");
  const langDropdown = document.getElementById("language-dropdown");

  if (!langToggle || !langDropdown) return;

  langToggle.addEventListener("click", (e) => {
    e.stopPropagation();
    langDropdown.classList.toggle("show");
  });

  // Language selection
  document.querySelectorAll(".language-option").forEach((option) => {
    option.addEventListener("click", function () {
      const lang = this.dataset.lang;
      switchLanguage(lang);
    });
  });

  // Close dropdown when clicking outside
  document.addEventListener("click", () => {
    langDropdown.classList.remove("show");
  });
}

function switchLanguage(lang) {
  // Show loading state
  const dropdown = document.getElementById("language-dropdown");
  dropdown.classList.remove("show");

  // Update active state immediately for better UX
  document.querySelectorAll(".language-option").forEach((opt) => {
    opt.classList.remove("active");
    const checkIcon = opt.querySelector(".fa-check");
    if (checkIcon) checkIcon.remove();
  });

  const activeOption = document.querySelector(`[data-lang="${lang}"]`);
  if (activeOption) {
    activeOption.classList.add("active");
    activeOption.innerHTML +=
      '<i class="fas fa-check" style="margin-left: auto; color: var(--primary);"></i>';
  }

  // Send AJAX request
  fetch("../app/ajax/set_language.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "language=" + lang,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Reload page to apply translations
        showToast("Language changed successfully", "success");
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      } else {
        showToast("Failed to change language", "error");
        // Revert active state on error
        document.querySelectorAll(".language-option").forEach((opt) => {
          opt.classList.remove("active");
        });
        const currentLang =
          document.querySelector("[data-lang].active") ||
          document.querySelector("[data-lang]");
        if (currentLang) currentLang.classList.add("active");
      }
    })
    .catch((error) => {
      console.error("Error switching language:", error);
      showToast("Network error changing language", "error");
    });
}

function upgradeToPro() {
  showToast("Please upgrade to pro to use this feature", "error");
}

// Toast notification function
function showToast(message, type = "info") {
  // Remove existing toasts
  const existingToasts = document.querySelectorAll(".global-toast");
  existingToasts.forEach((toast) => toast.remove());

  const toast = document.createElement("div");
  toast.className = `global-toast toast ${type}`;
  toast.innerHTML = `
        <i class="fas fa-${
          type === "success"
            ? "check-circle"
            : type === "error"
            ? "exclamation-circle"
            : "info-circle"
        }"></i>
        <span>${message}</span>
    `;

  document.body.appendChild(toast);

  // Animate in
  setTimeout(() => toast.classList.add("show"), 10);

  // Remove after delay
  setTimeout(() => {
    toast.classList.remove("show");
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// Theme Management
function initTheme() {
  const themeToggle = document.getElementById("theme-toggle");
  const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
  const savedTheme =
    localStorage.getItem("theme") || (prefersDark ? "dark" : "light");
    
    document.documentElement.classList.toggle(
      "light-mode",
      savedTheme === "light"
    );
    themeToggle.querySelector("i").className =
    savedTheme === "light" ? "fas fa-moon" : "fas fa-sun";
    
    themeToggle.addEventListener("click", () => {
    showToast("Theme Changed", "success");
    const isLight = document.documentElement.classList.toggle("light-mode");
    localStorage.setItem("theme", isLight ? "light" : "dark");
    themeToggle.querySelector("i").className = isLight
      ? "fas fa-moon"
      : "fas fa-sun";
  });
}

// User Dropdown
function initUserDropdown() {
  const userProfile = document.getElementById("user-profile");
  const userDropdown = document.getElementById("user-dropdown");

  userProfile.addEventListener("click", (e) => {
    e.stopPropagation();
    userDropdown.classList.toggle("show");
  });

  // Close dropdown when clicking outside
  document.addEventListener("click", () => {
    userDropdown.classList.remove("show");
  });
}

// Market Ticker
function initMarketTicker() {
  const tickerContainer = document.getElementById("market-ticker");
  const cryptocurrencies = [
    { symbol: "BTC", name: "Bitcoin", price: 45218.34, change: 2.34 },
    { symbol: "ETH", name: "Ethereum", price: 2387.56, change: 1.23 },
    { symbol: "USDT", name: "Tether", price: 1.0, change: 0.01 },
    { symbol: "USDC", name: "USD Coin", price: 1.0, change: 0.0 },
    { symbol: "SHIB", name: "Shiba Inu", price: 0.000035, change: -0.56 },
    { symbol: "SOL", name: "Solana", price: 102.45, change: 5.67 },
    { symbol: "XRP", name: "Ripple", price: 0.6234, change: -1.23 },
    { symbol: "ADA", name: "Cardano", price: 0.5123, change: 0.89 },
  ];

  // Create ticker items
  cryptocurrencies.forEach((crypto) => {
    const tickerItem = document.createElement("div");
    tickerItem.className = "ticker-item";

    const changeClass = crypto.change >= 0 ? "positive" : "negative";
    const changeIcon =
      crypto.change >= 0 ? "fas fa-caret-up" : "fas fa-caret-down";

    tickerItem.innerHTML = `
            <span class="ticker-symbol">${crypto.symbol}</span>
            <span class="ticker-price">$${crypto.price.toLocaleString()}</span>
            <span class="ticker-change ${changeClass}">
                <i class="${changeIcon}"></i>
                ${Math.abs(crypto.change)}%
            </span>
        `;

    tickerContainer.appendChild(tickerItem);
  });

  // Duplicate for seamless loop
  const clone = tickerContainer.cloneNode(true);
  tickerContainer.parentNode.appendChild(clone);
}

// Initialize everything when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  initTheme();
  // initLanguage();
  initUserDropdown();
  initMarketTicker();
  initLanguageSystem();

  // Add loading states
  document.querySelectorAll("button").forEach((button) => {
    button.addEventListener("click", function () {
      if (this.classList.contains("btn-primary")) {
        this.classList.add("loading");
        setTimeout(() => this.classList.remove("loading"), 2000);
      }
    });
  });
});

// Language Management
function initLanguage() {
  const langToggle = document.getElementById("language-toggle");
  const langDropdown = document.getElementById("language-dropdown");
  const savedLang = localStorage.getItem("language") || "en";

  // Set initial language
  setLanguage(savedLang);

  langToggle.addEventListener("click", (e) => {
    e.stopPropagation();
    langDropdown.classList.toggle("show");
  });

  document.querySelectorAll(".language-option").forEach((option) => {
    option.addEventListener("click", () => {
      const lang = option.dataset.lang;
      setLanguage(lang);
      langDropdown.classList.remove("show");
    });
  });

  // Close dropdown when clicking outside
  document.addEventListener("click", () => {
    langDropdown.classList.remove("show");
  });
}

function setLanguage(lang) {
  localStorage.setItem("language", lang);
  document.documentElement.lang = lang;

  // Load language file and update texts
  loadLanguageFile(lang);
}

function loadLanguageFile(lang) {
  fetch(`../assets/lang/${lang}.json`)
    .then((response) => response.json())
    .then((translations) => {
      document.querySelectorAll("[data-i18n]").forEach((element) => {
        const key = element.getAttribute("data-i18n");
        if (translations[key]) {
          element.textContent = translations[key];
        }
      });
    })
    .catch(() => {
      console.warn(`Language file for ${lang} not found`);
    });
}
