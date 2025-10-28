    <?php if (isset($_SESSION['show_processing_modal'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Show the processing modal
                document.getElementById('processing-modal').classList.add('active');
                // Unset the session variable so it doesn't show again on refresh
                <?php unset($_SESSION['show_processing_modal']); ?>
            });
        </script>
    <?php endif; ?>
    <script>
        // Current selected cryptocurrency and action
        let selectedCrypto = null;
        let selectedAction = null;
        let assets = <?php echo json_encode($assets); ?>;

        // Toast notification function
        function showToast(message) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');

            toastMessage.textContent = message;
            toast.classList.add('show');

            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        function closeProcessingModal() {
            document.getElementById('processing-modal').classList.remove('active');
        }

        // Open Action Selection Modal
        // document.getElementById("action-btn").addEventListener("click", () => {
        //     document.getElementById("action-selection-modal").classList.add("active");
        // });

        // Open Crypto Selection Modal based on action
        document.getElementById("select-send").addEventListener("click", () => {
            selectedAction = 'send';
            document.getElementById("action-selection-modal").classList.remove("active");
            document.getElementById("crypto-selection-modal").classList.add("active");
            document.getElementById("selection-modal-title").textContent = "Select Cryptocurrency to Send";
            document.getElementById("selection-modal-description").textContent = "Choose which cryptocurrency you want to send";
        });

        document.getElementById("select-receive").addEventListener("click", () => {
            selectedAction = 'receive';
            document.getElementById("action-selection-modal").classList.remove("active");
            document.getElementById("crypto-selection-modal").classList.add("active");
            document.getElementById("selection-modal-title").textContent = "Select Cryptocurrency to Receive";
            document.getElementById("selection-modal-description").textContent = "Choose which cryptocurrency you want to receive";
        });

        // Close modals
        document.querySelectorAll(".modal-close").forEach((button) => {
            console.log('Working');  
            button.addEventListener("click", () => {
                document.querySelectorAll(".modal").forEach((modal) => {
                    modal.classList.remove("active");
                });
            });
        });

        // Crypto selection in selection modal
        document.querySelectorAll(".crypto-option").forEach((option) => {
            option.addEventListener("click", () => {
                // Remove selected class from all options
                document.querySelectorAll(".crypto-option").forEach((opt) => {
                    opt.classList.remove("selected");
                });

                // Add selected class to clicked option
                option.classList.add("selected");

                // Enable continue button
                document.getElementById("confirm-crypto-selection").disabled = false;

                // Get selected crypto data
                const cryptoId = option.getAttribute("data-crypto-id");
                const symbol = option.getAttribute("data-symbol");

                // Find the asset in our assets array
                selectedCrypto = assets.find(asset => asset.id == cryptoId);
            });
        });

        // Send and Receive buttons on asset cards
        document.querySelectorAll(".send-btn").forEach((btn) => {
            btn.addEventListener("click", (e) => {
                e.stopPropagation();
                const symbol = btn.getAttribute("data-asset");
                const assetId = btn.getAttribute("data-asset-id");

                // Find the asset in our assets array
                selectedCrypto = assets.find(asset => asset.symbol.toLowerCase() === symbol);
                selectedAction = 'send';

                if (selectedCrypto) {
                    // Open send modal with selected crypto
                    document.getElementById("send-crypto-modal").classList.add("active");
                    document.getElementById("send-crypto-name").textContent = selectedCrypto.symbol;
                    document.getElementById("selected-crypto-id").value = selectedCrypto.id;
                    document.getElementById("available-balance").textContent =
                        `Available: ${selectedCrypto.balance} ${selectedCrypto.symbol}`;

                    // Update transaction summary
                    updateTransactionSummary();
                }
            });
        });

        document.querySelectorAll(".receive-btn").forEach((btn) => {
            btn.addEventListener("click", (e) => {
                e.stopPropagation();
                const symbol = btn.getAttribute("data-asset");
                const assetId = btn.getAttribute("data-asset-id");

                // Find the asset in our assets array
                selectedCrypto = assets.find(asset => asset.id == assetId);
                selectedAction = 'receive';

                if (selectedCrypto) {
                    // Open receive modal with selected crypto
                    document.getElementById("receive-crypto-modal").classList.add("active");
                    document.getElementById("receive-title").textContent = `Receive ${selectedCrypto.symbol}`;
                    // document.getElementById("receive-crypto-name").textContent = selectedCrypto.symbol;

                    // Debug: Log the selected crypto data
                    console.log('Selected Crypto (direct button):', selectedCrypto);

                    // Set the address from admin settings - FIXED
                    // const receivingAddress = selectedCrypto.receiving_address;
                    document.getElementById("receive-address").textContent = selectedCrypto.receiving_address;

                    // Set QR code - use admin-set QR code if available, otherwise generate from address - FIXED
                    const qrCodeImg = document.getElementById("receive-qr-code");
                    if (selectedCrypto.qr_code && selectedCrypto.qr_code.trim() !== '') {
                        qrCodeImg.src = selectedCrypto.qr_code;
                        qrCodeImg.style.display = "block";
                        console.log('Using admin QR code (direct button):', selectedCrypto.qr_code);
                    } else if (receivingAddress && receivingAddress.trim() !== '') {
                        qrCodeImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(receivingAddress)}`;
                        qrCodeImg.style.display = "block";
                        console.log('Generated QR code from address (direct button)');
                    } else {
                        qrCodeImg.style.display = "none";
                        console.log('No QR code available (direct button)');
                    }

                    // Update warning message
                    document.getElementById("receive-warning").innerHTML =
                        `<i class="fas fa-exclamation-triangle"></i> Only send ${selectedCrypto.symbol} to this address. Sending other assets may result in permanent loss.`;
                }
            });
        });

        // Confirm crypto selection
        document.getElementById("confirm-crypto-selection").addEventListener("click", () => {            
            if (selectedCrypto && selectedAction) {
                document.getElementById("crypto-selection-modal").classList.remove("active");

                if (selectedAction === 'send') {
                    // Open send modal with selected crypto
                    document.getElementById("send-crypto-modal").classList.add("active");
                    document.getElementById("send-crypto-name").textContent = selectedCrypto.symbol;
                    document.getElementById("selected-crypto-id").value = selectedCrypto.id;
                    document.getElementById("available-balance").textContent =
                        `Available: ${selectedCrypto.balance} ${selectedCrypto.symbol}`;

                    // Update transaction summary
                    updateTransactionSummary();
                }
            }
        });

        // Amount input change
        document.getElementById("send-amount").addEventListener("input", function() {
            updateTransactionSummary();
        });

        // Update transaction summary
        function updateTransactionSummary() {
            if (selectedCrypto) {
                const amount = parseFloat(document.getElementById("send-amount").value) || 0;
                const networkFee = Math.max(amount * 0.001, 0.0001);
                const netAmount = amount - networkFee;

                // Update summary
                document.getElementById("summary-amount").textContent =
                    `${amount.toFixed(8)} ${selectedCrypto.symbol}`;
                document.getElementById("summary-fee").textContent =
                    `${networkFee.toFixed(8)} ${selectedCrypto.symbol}`;
                document.getElementById("summary-net").textContent =
                    `${netAmount.toFixed(8)} ${selectedCrypto.symbol}`;

                // Update speed up cost (0.0005 of the selected cryptocurrency)
                document.getElementById("speed-up-cost").textContent =
                    `+ 0.00050000 ${selectedCrypto.symbol}`;
            }
        }

        // Copy address button
        document.getElementById("copy-address-btn").addEventListener("click", function() {
            if (selectedCrypto && selectedCrypto.receiving_address) {
                navigator.clipboard.writeText(selectedCrypto.receiving_address).then(() => {
                    showToast("Address copied to clipboard!");
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                    showToast("Failed to copy address");
                });
            } else {
                showToast("No address available to copy");
            }
        });

        // Share address button
        document.getElementById("share-address-btn").addEventListener("click", function() {
            if (selectedCrypto && selectedCrypto.receiving_address) {
                if (navigator.share) {
                    navigator.share({
                        title: `My ${selectedCrypto.symbol} Address`,
                        text: `Here is my ${selectedCrypto.symbol} address for receiving payments:`,
                        url: selectedCrypto.receiving_address,
                    }).then(() => {
                        showToast("Address shared successfully!");
                    }).catch(err => {
                        console.error('Error sharing:', err);
                        showToast("Failed to share address");
                    });
                } else {
                    // Fallback to copying if Web Share API is not supported
                    navigator.clipboard.writeText(selectedCrypto.receiving_address).then(() => {
                        showToast("Address copied to clipboard (sharing not supported)");
                    }).catch(err => {
                        console.error('Failed to copy: ', err);
                        showToast("Failed to copy address");
                    });
                }
            } else {
                showToast("No address available to share");
            }
        });

        // Processing Modal Functions
        function showProcessingModal(transactionId, crypto, amount, fee, recipient) {
            document.getElementById('processing-id').textContent = '#' + transactionId;
            document.getElementById('processing-crypto').textContent = crypto;
            document.getElementById('processing-amount').textContent = amount + ' ' + crypto;
            document.getElementById('processing-fee').textContent = fee + ' ' + crypto;
            document.getElementById('processing-recipient').textContent = recipient;

            // Update buttons with transaction ID
            document.getElementById('processing-speed-up-btn').setAttribute('data-transaction-id', transactionId);
            document.getElementById('processing-cancel-btn').setAttribute('data-transaction-id', transactionId);

            document.getElementById('processing-modal').classList.add('active');
        }

        function closeProcessingModal() {
            document.getElementById('processing-modal').classList.remove('active');
        }

        function viewTransactionHistory() {
            closeProcessingModal();
            // Scroll to transaction history section
            document.querySelector('.transaction-list').scrollIntoView({
                behavior: 'smooth'
            });
        }

        // Close processing modal when clicking the X button
        // document.getElementById('close-processing-modal').addEventListener('click', closeProcessingModal);

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === document.getElementById('processing-modal')) {
                closeProcessingModal();
            }
        });

    
        // Speed up transaction from processing modal
        document.getElementById('processing-speed-up-btn').addEventListener('click', function() {
            const transactionId = this.getAttribute('data-transaction-id');
            if (confirm('Speed up this transaction? This will prioritize it for faster processing.')) {
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                this.disabled = true;

                fetch('speed_up_transaction.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            transaction_id: transactionId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Transaction has been prioritized for faster processing!');
                            this.innerHTML = '<i class="fas fa-bolt"></i> Sped Up!';
                            this.disabled = true;
                            this.style.backgroundColor = '#10b981';
                        } else {
                            showToast('Error: ' + data.error);
                            this.innerHTML = '<i class="fas fa-bolt"></i> Speed Up Transaction';
                            this.disabled = false;
                        }
                    })
                    .catch(error => {
                        showToast('Error speeding up transaction');
                        this.innerHTML = '<i class="fas fa-bolt"></i> Speed Up Transaction';
                        this.disabled = false;
                    });
            }
        });

        // Cancel transaction from processing modal
        document.getElementById('processing-cancel-btn').addEventListener('click', function() {
            const transactionId = this.getAttribute('data-transaction-id');
            if (confirm('Are you sure you want to cancel this transaction? The funds will be returned to your account.')) {
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cancelling...';
                this.disabled = true;

                fetch('cancel_transaction.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            transaction_id: transactionId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Transaction cancelled successfully! Funds have been returned to your account.');
                            closeProcessingModal();
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            showToast('Error: ' + data.error);
                            this.innerHTML = '<i class="fas fa-times"></i> Cancel Transaction';
                            this.disabled = false;
                        }
                    })
                    .catch(error => {
                        showToast('Error cancelling transaction');
                        this.innerHTML = '<i class="fas fa-times"></i> Cancel Transaction';
                        this.disabled = false;
                    });
            }
        });

        // Show processing modal automatically after successful form submission
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['show_processing_modal']) && $_SESSION['show_processing_modal']): ?>
                // Show the processing modal with transaction details
                showProcessingModal(
                    '<?php echo $_SESSION['processing_transaction_id']; ?>',
                    '<?php echo $_SESSION['processing_crypto_symbol']; ?>',
                    '<?php echo number_format($_SESSION['processing_amount'], 8); ?>',
                    '<?php echo number_format($_SESSION['processing_network_fee'], 8); ?>',
                    '<?php echo substr($_SESSION['processing_wallet_address'], 0, 20) . '...'; ?>'
                );

                // Clear the session variables
                <?php
                unset($_SESSION['show_processing_modal']);
                unset($_SESSION['processing_transaction_id']);
                unset($_SESSION['processing_crypto_symbol']);
                unset($_SESSION['processing_amount']);
                unset($_SESSION['processing_network_fee']);
                unset($_SESSION['processing_wallet_address']);
                ?>
            <?php endif; ?>
        });

        // Speed Up Transaction button in transaction history
        document.querySelectorAll(".speed-up-btn").forEach(button => {
            button.addEventListener("click", function() {
                const transactionId = this.getAttribute("data-transaction-id");
                if (confirm("Speed up this transaction? This will prioritize it for processing at an additional cost.")) {
                    // Simulate speed up process
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing';
                    this.disabled = true;

                    // In a real application, this would make an API call
                    fetch('speed_up_transaction.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                transaction_id: transactionId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showToast(`Transaction #${transactionId} has been prioritized and will be processed faster.`);
                                this.innerHTML = '<i class="fas fa-bolt"></i> Sped Up';
                                this.disabled = true;
                            } else {
                                showToast('Error: ' + data.error);
                                this.innerHTML = 'Speed Up';
                                this.disabled = false;
                            }
                        })
                        .catch(error => {
                            showToast('Error speeding up transaction');
                            this.innerHTML = 'Speed Up';
                            this.disabled = false;
                        });
                }
            });
        });

        // Cancel Transaction button in transaction history
        document.querySelectorAll(".cancel-btn").forEach(button => {
            button.addEventListener("click", function() {
                const transactionId = this.getAttribute("data-transaction-id");
                if (confirm("Are you sure you want to cancel this transaction? This action cannot be undone.")) {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cancelling';
                    this.disabled = true;

                    fetch('cancel_transaction.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                transaction_id: transactionId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showToast("Transaction cancelled successfully!");
                                location.reload();
                            } else {
                                showToast('Error: ' + data.error);
                                this.innerHTML = 'Cancel';
                                this.disabled = false;
                            }
                        })
                        .catch(error => {
                            showToast('Error cancelling transaction');
                            this.innerHTML = 'Cancel';
                            this.disabled = false;
                        });
                }
            });
        });

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        });
    </script>