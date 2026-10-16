(function () {
    const button = document.getElementById('pay-button');
    if (!button || button.dataset.paymentBound === 'true') {
        return;
    }

    button.dataset.paymentBound = 'true';

    const bookId = button.dataset.bookId;
    const csrfToken = button.dataset.csrfToken;
    const buyUrl = '/books/' + bookId + '/buy';
    const checkUrl = '/books/' + bookId + '/check-payment';
    let pendingOrderId = button.dataset.pendingOrderId || null;
    let paymentPollTimer = null;
    let paymentPollCount = 0;
    let paymentCheckInFlight = false;
    let activeOrderId = pendingOrderId;

    const postBuy = (payload) => fetch(buyUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    }).then(async (response) => ({
        ok: response.ok,
        status: response.status,
        data: await response.json().catch(() => null)
    }));

    const resetButton = () => {
        button.disabled = false;
        button.textContent = pendingOrderId
            ? 'Periksa Status Pembayaran'
            : 'Beli & Bayar Sekarang';
    };

    const startPaymentPolling = (orderId) => {
        if (!orderId) {
            return;
        }

        activeOrderId = orderId;
        if (paymentPollTimer) {
            return;
        }

        paymentPollCount = 0;
        paymentPollTimer = window.setInterval(() => {
            paymentPollCount += 1;
            if (paymentPollCount > 360) {
                window.clearInterval(paymentPollTimer);
                paymentPollTimer = null;
                resetButton();
                return;
            }
            checkPendingPayment(activeOrderId);
        }, 4000);
    };

    const checkPendingPayment = (orderId, notifyOnUnavailable = false) => {
        if (!orderId) {
            return Promise.resolve();
        }

        if (paymentCheckInFlight) {
            return Promise.resolve();
        }

        paymentCheckInFlight = true;
        return fetch(checkUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ order_id: orderId })
        })
            .then((response) => response.json())
            .then((data) => {
                if (data && data.status_unavailable) {
                    if (paymentPollTimer) {
                        window.clearInterval(paymentPollTimer);
                        paymentPollTimer = null;
                    }
                    resetButton();
                    if (notifyOnUnavailable) {
                        alert(data.message || 'Status pembayaran belum dapat diverifikasi ke Midtrans.');
                    }
                    return;
                }

                if (data && data.success) {
                    if (paymentPollTimer) {
                        window.clearInterval(paymentPollTimer);
                        paymentPollTimer = null;
                    }
                    window.location.reload();
                    return;
                }

                if (data && data.pending) {
                    resetButton();
                    startPaymentPolling(orderId);
                } else {
                    if (paymentPollTimer) {
                        window.clearInterval(paymentPollTimer);
                        paymentPollTimer = null;
                    }
                    resetButton();
                }
                if (notifyOnUnavailable) {
                    alert((data && data.message) || 'Pembayaran belum terkonfirmasi.');
                }
            })
            .catch(() => {
                // Ignore network glitch & continue polling
            })
            .finally(() => {
                paymentCheckInFlight = false;
            });
    };

    const confirmPayment = (result, voucherCode) => {
        postBuy({
            voucher_code: voucherCode,
            order_id: result && result.order_id ? result.order_id : null,
            payment_completed: true
        }).then((response) => {
            if (response.ok && response.data && response.data.success) {
                window.location.reload();
                return;
            }

            pendingOrderId = activeOrderId;
            startPaymentPolling(result && result.order_id ? result.order_id : activeOrderId);
            checkPendingPayment(activeOrderId);
        }).catch(() => {
            pendingOrderId = activeOrderId;
            startPaymentPolling(activeOrderId);
            checkPendingPayment(activeOrderId);
        });
    };

    button.addEventListener('click', () => {
        if (pendingOrderId) {
            button.disabled = true;
            button.textContent = 'Memeriksa Pembayaran...';
            checkPendingPayment(pendingOrderId, true);
            return;
        }

        const voucherInput = document.getElementById('voucher_code_input');
        const voucherCode = voucherInput ? voucherInput.value : '';

        button.disabled = true;
        button.textContent = 'Memproses...';

        postBuy({ voucher_code: voucherCode })
            .then((response) => {
                const data = response.data;
                if (!data || !data.success || !data.snap_token) {
                    if (data && data.message && data.message.toLowerCase().includes('sudah membeli')) {
                        window.location.reload();
                        return;
                    }
                    alert((data && data.message) || 'Gagal mendapatkan token pembayaran.');
                    resetButton();
                    return;
                }

                activeOrderId = data.order_id;
                pendingOrderId = data.order_id;

                if (!window.snap) {
                    alert('Midtrans belum siap. Muat ulang halaman lalu coba lagi.');
                    resetButton();
                    return;
                }

                window.snap.pay(data.snap_token, {
                    onSuccess: (result) => confirmPayment(result, voucherCode),
                    onPending: (result) => {
                        resetButton();
                        startPaymentPolling(result && result.order_id ? result.order_id : activeOrderId);
                        checkPendingPayment(activeOrderId);
                    },
                    onError: () => {
                        alert('Pembayaran gagal. Silakan coba lagi.');
                        pendingOrderId = null;
                        activeOrderId = null;
                        resetButton();
                    },
                    onClose: () => {
                        resetButton();
                        startPaymentPolling(activeOrderId);
                        checkPendingPayment(activeOrderId);
                    }
                });
            })
            .catch(() => {
                alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
                resetButton();
            });
    });

}());
