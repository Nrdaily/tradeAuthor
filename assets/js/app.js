// Theme toggle functionality
const themeToggle = document.getElementById("theme-toggle");
const themeIcon = themeToggle.querySelector("i");

themeToggle.addEventListener("click", () => {
  document.body.classList.toggle("dark-mode");
  if (document.body.classList.contains("dark-mode")) {
    themeIcon.classList.remove("fa-moon");
    themeIcon.classList.add("fa-sun");
    localStorage.setItem("theme", "dark");
  } else {
    themeIcon.classList.remove("fa-sun");
    themeIcon.classList.add("fa-moon");
    localStorage.setItem("theme", "light");
  }
});

// Check for saved theme preference
if (localStorage.getItem("theme") === "dark") {
  document.body.classList.add("dark-mode");
  themeIcon.classList.remove("fa-moon");
  themeIcon.classList.add("fa-sun");
}

// // User dropdown functionality
// const userProfile = document.getElementById("user-profile");
// const userDropdown = document.getElementById("user-dropdown");

// userProfile.addEventListener("click", function (e) {
//   e.stopPropagation();
//   userDropdown.classList.toggle("show");
// });

// Mobile menu toggle
document.getElementById("menu-toggle").addEventListener("click", () => {
  document.getElementById("sidebar").classList.toggle("active");
});

// Refresh buttons
document
  .getElementById("refresh-portfolio")
  .addEventListener("click", function () {
    const icon = this.querySelector("i");
    icon.classList.add("fa-spin");

    // Simulate API call
    setTimeout(() => {
      icon.classList.remove("fa-spin");

      // Show notification
      showNotification("Portfolio data updated successfully", "success");
    }, 1500);
  });

document
  .getElementById("refresh-market")
  .addEventListener("click", function () {
    const icon = this.querySelector("i");
    icon.classList.add("fa-spin");

    // Simulate API call
    setTimeout(() => {
      icon.classList.remove("fa-spin");

      // Show notification
      showNotification("Market data updated successfully", "success");
    }, 1500);
  });

// Create request button
// document.getElementById("create-request-btn").addEventListener("click", () => {
//   navigateTo("request-payment");
// });

// Notification function
function showNotification(message, type) {
  const notification = document.createElement("div");
  notification.style.position = "fixed";
  notification.style.bottom = "20px";
  notification.style.right = "20px";
  notification.style.padding = "12px 20px";
  notification.style.borderRadius = "8px";
  notification.style.color = "white";
  notification.style.boxShadow = "0 4px 12px rgba(0,0,0,0.15)";
  notification.style.zIndex = "1000";
  notification.style.opacity = "0";
  notification.style.transform = "translateY(20px)";
  notification.style.transition = "opacity 0.3s, transform 0.3s";

  if (type === "success") {
    notification.style.background = "var(--success)";
  } else if (type === "error") {
    notification.style.background = "var(--danger)";
  } else {
    notification.style.background = "var(--primary)";
  }

  notification.innerHTML = `
                <i class="fas ${
                  type === "success"
                    ? "fa-check-circle"
                    : "fa-exclamation-circle"
                }"></i>
                ${message}
            `;

  document.body.appendChild(notification);

  // Animate in
  setTimeout(() => {
    notification.style.opacity = "1";
    notification.style.transform = "translateY(0)";
  }, 10);

  // Remove after 3 seconds
  setTimeout(() => {
    notification.style.opacity = "0";
    notification.style.transform = "translateY(20px)";

    setTimeout(() => {
      document.body.removeChild(notification);
    }, 300);
  }, 3000);
}