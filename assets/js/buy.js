'use strict';
// Buy page functionality
const cryptoOptions = document.querySelectorAll("[data-crypto-op]");
const amountInput = document.getElementById("amount");
const conversionElement = document.getElementById("conversion");
const youPayElement = document.getElementById("you-pay");
const youGetElement = document.getElementById("you-get");
const totalAmountElement = document.getElementById("total-amount");
const amountButtons = document.querySelectorAll(".amount-btn");
const checkoutButton = document.getElementById("checkout-btn");

let selectedCrypto = "usdt";
let cryptoPrice = 1.0;
const processingFee = 6.0;

// Crypto selection
cryptoOptions.forEach((option) => {
  option.addEventListener("click", () => {
    cryptoOptions.forEach((opt) => opt.classList.remove("selected"));
    option.classList.add("selected");

    selectedCrypto = option.getAttribute("data-crypto");
    cryptoPrice = parseFloat(option.getAttribute("data-price"));

    updateCalculation();
  });
});

// Amount input
amountInput.addEventListener("input", () => {
  // Ensure minimum amount
  if (parseFloat(amountInput.value) < 6700) {
    amountInput.value = 6700;
  }

  updateCalculation();

  // Update active amount button
  amountButtons.forEach((btn) => {
    if (
      parseFloat(btn.getAttribute("data-amount")) ===
      parseFloat(amountInput.value)
    ) {
      btn.classList.add("active");
    } else {
      btn.classList.remove("active");
    }
  });
});

// Amount buttons
amountButtons.forEach((btn) => {
  btn.addEventListener("click", () => {
    const amount = btn.getAttribute("data-amount");
    amountInput.value = amount;

    amountButtons.forEach((b) => b.classList.remove("active"));
    btn.classList.add("active");

    updateCalculation();
  });
});

// Update calculation
function updateCalculation() {
  const amount = parseFloat(amountInput.value);
  const cryptoAmount = amount / cryptoPrice;
  const total = amount + processingFee;

  youPayElement.textContent = `$${amount.toFixed(2)}`;
  youGetElement.textContent = `${cryptoAmount.toFixed(
    2
  )} ${selectedCrypto.toUpperCase()}`;
  conversionElement.textContent = `≈ ${cryptoAmount.toFixed(
    2
  )} ${selectedCrypto.toUpperCase()}`;
  totalAmountElement.textContent = `$${total.toFixed(2)}`;

  // Enable/disable checkout button based on amount
  checkoutButton.disabled = amount < 6700;
}

// Checkout button
checkoutButton.addEventListener("click", () => {
  // Update checkout modal with current values
  document.getElementById("checkout-amount").textContent = `$${parseFloat(
    amountInput.value
  ).toFixed(2)}`;
  document.getElementById("checkout-receive").textContent = `${(
    parseFloat(amountInput.value) / cryptoPrice
  ).toFixed(2)} ${selectedCrypto.toUpperCase()}`;
  document.getElementById(
    "checkout-fee"
  ).textContent = `$${processingFee.toFixed(2)}`;
  document.getElementById("checkout-total").textContent = `$${(
    parseFloat(amountInput.value) + processingFee
  ).toFixed(2)}`;

  // Show checkout modal
  document.getElementById("checkout-modal").classList.add("active");
});

// Close checkout modal
document.getElementById("close-checkout").addEventListener("click", () => {
  document.getElementById("checkout-modal").classList.remove("active");
});

// Payment form validation and submission
document
  .getElementById("payment-form")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    // Basic validation
    let isValid = true;

    // Card number validation (simple version)
    const cardNumber = document.getElementById("card-number");
    if (!cardNumber.value || cardNumber.value.replace(/\s/g, "").length < 16) {
      cardNumber.classList.add("input-error");
      document.getElementById("card-number-error").style.display = "block";
      isValid = false;
    } else {
      cardNumber.classList.remove("input-error");
      document.getElementById("card-number-error").style.display = "none";
    }

    // Expiry date validation
    const expiryDate = document.getElementById("expiry-date");
    if (!expiryDate.value || !/^\d{2}\/\d{2}$/.test(expiryDate.value)) {
      expiryDate.classList.add("input-error");
      document.getElementById("expiry-date-error").style.display = "block";
      isValid = false;
    } else {
      expiryDate.classList.remove("input-error");
      document.getElementById("expiry-date-error").style.display = "none";
    }

    // CVV validation
    const cvv = document.getElementById("cvv");
    if (!cvv.value || cvv.value.length < 3) {
      cvv.classList.add("input-error");
      document.getElementById("cvv-error").style.display = "block";
      isValid = false;
    } else {
      cvv.classList.remove("input-error");
      document.getElementById("cvv-error").style.display = "none";
    }

    // Cardholder name validation
    const cardholderName = document.getElementById("cardholder-name");
    if (!cardholderName.value) {
      cardholderName.classList.add("input-error");
      document.getElementById("cardholder-name-error").style.display = "block";
      isValid = false;
    } else {
      cardholderName.classList.remove("input-error");
      document.getElementById("cardholder-name-error").style.display = "none";
    }

    if (isValid) {
      // Show processing screen
      document.getElementById("checkout-summary").style.display = "none";
      document.getElementById("checkout-processing").style.display = "block";

      // Simulate processing
      setTimeout(() => {
        // Show success notification
        showNotification("Payment processed successfully!", "success");

        // Close modal after processing
        setTimeout(() => {
          document.getElementById("checkout-modal").classList.remove("active");
          document.getElementById("checkout-summary").style.display = "block";
          document.getElementById("checkout-processing").style.display = "none";

          // Reset form
          document.getElementById("payment-form").reset();
        }, 2000);
      }, 3000);
    }
  });

  
// Format card number input
document.getElementById("card-number").addEventListener("input", function (e) {
  let value = e.target.value.replace(/\D/g, "");
  if (value.length > 16) value = value.slice(0, 16);

  // Add spaces for better readability
  value = value.replace(/(\d{4})/g, "$1 ").trim();
  e.target.value = value;
});

// Format expiry date input
document.getElementById("expiry-date").addEventListener("input", function (e) {
  let value = e.target.value.replace(/\D/g, "");
  if (value.length > 4) value = value.slice(0, 4);

  if (value.length > 2) {
    value = value.slice(0, 2) + "/" + value.slice(2);
  }
  e.target.value = value;
});


// Initialize calculations
updateCalculation();
