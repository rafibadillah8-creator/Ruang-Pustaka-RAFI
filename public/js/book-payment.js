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
    let paymentPollTimer = null;
    let paymentPollCount = 0;
    let paymentCheckInFlight = false;

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
        button.textContent = 'Beli & Bayar Sekarang';
    };

    const startPaymentPolling = () => {
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
            checkPendingPayment();
        }, 4000);
    };

    const checkPendingPayment = () => {
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
            body: '{}'
        })
            .then((response) => response.json())
            .then((data) => {
                if (data && data.success) {
                    if (paymentPollTimer) {
                        window.clearInterval(paymentPollTimer);
                        paymentPollTimer = null;
                    }
                    window.location.reload();
                    return;
                }

                if (data && data.pending) {
                    startPaymentPolling();
                } else {
                    if (paymentPollTimer) {
                        window.clearInterval(paymentPollTimer);
                        paymentPollTimer = null;
                    }
                    resetButton();
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

            startPaymentPolling();
            checkPendingPayment();
        }).catch(() => {
            startPaymentPolling();
            checkPendingPayment();
        });
    };

    button.addEventListener('click', () => {
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

                if (!window.snap) {
                    alert('Midtrans belum siap. Muat ulang halaman lalu coba lagi.');
                    resetButton();
                    return;
                }

                // Mulai polling otomatis di latar belakang begitu token dibuat
                startPaymentPolling();

                window.snap.pay(data.snap_token, {
                    onSuccess: (result) => confirmPayment(result, voucherCode),
                    onPending: () => {
                        resetButton();
                        startPaymentPolling();
                        checkPendingPayment();
                    },
                    onError: () => {
                        alert('Pembayaran gagal. Silakan coba lagi.');
                        resetButton();
                    },
                    onClose: () => {
                        resetButton();
                        startPaymentPolling();
                        checkPendingPayment();
                    }
                });
            })
            .catch(() => {
                alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
                resetButton();
            });
    });

    checkPendingPayment();
}());
