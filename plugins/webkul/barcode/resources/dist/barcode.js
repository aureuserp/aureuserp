(() => {
    window.BarcodeUiState = window.BarcodeUiState || {
        lastLocatedRecordKey: null,
    };

    window.BarcodeGlobal = window.BarcodeGlobal || {};

    if (! window.BarcodeGlobal.alpineRegistered) {
        document.addEventListener('alpine:init', () => {
            Alpine.data('barcodeScanner', (property = 'barcode', action = 'scan') => ({
                active: false,
                processing: false,
                scanner: null,
                scannerError: '',
                property,
                action,
                confirmPending: null,
                confirmLabel: '',
                confirmMode: null,
                backorderMoveLines: [],
                actionMenuOpen: false,

                init() {
                    this.handleNativeScanRequest = async () => {
                        this.scannerError = '';

                        if (this.active || this.processing) {
                            return;
                        }

                        await this.start(this.$wire);
                    };

                    window.addEventListener('barcode-native-scan-request', this.handleNativeScanRequest);
                },

                destroy() {
                    if (this.handleNativeScanRequest) {
                        window.removeEventListener('barcode-native-scan-request', this.handleNativeScanRequest);
                    }
                },

                requestAction(key, label) {
                    this.actionMenuOpen = false;
                    this.confirmPending = key;
                    this.confirmLabel = label;
                    this.confirmMode = 'simple';
                    this.backorderMoveLines = [];
                },

                requestValidate(label, backorderMoveLines, hasAnyCounted, shouldAskBackorder) {
                    this.actionMenuOpen = false;
                    this.confirmPending = 'validate';
                    this.confirmLabel = label;
                    this.backorderMoveLines = backorderMoveLines;
                    this.confirmMode = shouldAskBackorder && hasAnyCounted && backorderMoveLines.length > 0
                        ? 'backorder'
                        : 'simple';
                },

                cancelAction() {
                    this.confirmPending = null;
                    this.confirmLabel = '';
                    this.confirmMode = null;
                    this.backorderMoveLines = [];
                    this.actionMenuOpen = false;
                },

                toggleActionMenu() {
                    this.actionMenuOpen = ! this.actionMenuOpen;
                },

                closeActionMenu() {
                    this.actionMenuOpen = false;
                },

                renderScannerError(message) {
                    this.scannerError = message;
                },

                async toggle($wire) {
                    if (this.active || this.scannerError) {
                        await this.stop();

                        return;
                    }

                    await this.start($wire);
                },

                async start($wire) {
                    this.active = true;
                    this.scannerError = '';
                    this.processing = false;

                    window.BarcodeUiState.lastLocatedRecordKey = null;
                    window.BarcodeGlobal?.forceScrollTop?.();

                    await this.$nextTick();
                    await new Promise((resolve) => window.requestAnimationFrame(resolve));

                    window.BarcodeGlobal?.forceScrollTop?.();

                    if (! window.Html5Qrcode) {
                        this.active = false;
                        this.renderScannerError('Scanner library failed to load.');

                        return;
                    }

                    this.scanner = new Html5Qrcode('barcode-reader');

                    try {
                        await this.scanner.start(
                            { facingMode: 'environment' },
                            { fps: 10, qrbox: { width: 260, height: 180 } },
                            async (decodedText) => {
                                if (this.processing) {
                                    return;
                                }

                                this.processing = true;
                                await this.stop();
                                $wire.set(this.property, decodedText);
                                await $wire[this.action]();
                                this.processing = false;
                            },
                        );
                    } catch (error) {
                        console.error('Barcode scanner failed to start.', error);
                        this.active = false;
                        this.processing = false;

                        try {
                            this.scanner?.clear();
                        } catch (clearError) {
                            console.error('Barcode scanner cleanup failed.', clearError);
                        }

                        this.scanner = null;

                        const errorMessage = error?.message ?? String(error) ?? 'Unable to access the camera.';

                        this.renderScannerError(errorMessage);
                        window.BarcodeNative?.toast?.(errorMessage, 'short');
                    }
                },

                async stop() {
                    this.active = false;
                    this.processing = false;
                    this.scannerError = '';

                    if (this.scanner) {
                        try {
                            await this.scanner.stop();
                        } catch (error) {
                            console.error('Barcode scanner failed to stop cleanly.', error);
                        }

                        try {
                            this.scanner.clear();
                        } catch (error) {
                            console.error('Barcode scanner clear failed.', error);
                        }

                        this.scanner = null;
                    }

                    const reader = document.getElementById('barcode-reader');

                    if (reader) {
                        reader.innerHTML = '';
                    }
                },
            }));
        });

        window.BarcodeGlobal.alpineRegistered = true;
    }

    if (! window.BarcodeGlobal.dispatchNativeScanRequestFromHash) {
        if ('scrollRestoration' in window.history) {
            window.history.scrollRestoration = 'manual';
        }

        window.BarcodeGlobal.forceScrollTop = () => {
            const active = document.activeElement;
            if (active && typeof active.blur === 'function' && active !== document.body) {
                active.blur();
            }

            const html = document.documentElement;
            const body = document.body;
            const prevHtmlBehavior = html.style.scrollBehavior;
            const prevBodyBehavior = body.style.scrollBehavior;

            html.style.scrollBehavior = 'auto';
            body.style.scrollBehavior = 'auto';

            (document.scrollingElement || html).scrollTop = 0;
            html.scrollTop = 0;
            body.scrollTop = 0;
            window.scrollTo(0, 0);

            document.querySelectorAll('main').forEach((el) => {
                el.scrollTop = 0;
            });

            html.style.scrollBehavior = prevHtmlBehavior;
            body.style.scrollBehavior = prevBodyBehavior;
        };

        window.BarcodeGlobal.dispatchNativeScanRequestFromHash = () => {
            if (window.location.hash !== '#scan-barcode') {
                return;
            }

            const hashlessUrl = `${window.location.pathname}${window.location.search}`;

            window.history.replaceState({}, document.title, hashlessUrl);
            window.dispatchEvent(new CustomEvent('barcode-native-scan-request'));
        };

        window.addEventListener('hashchange', window.BarcodeGlobal.dispatchNativeScanRequestFromHash);
        window.addEventListener('load', window.BarcodeGlobal.dispatchNativeScanRequestFromHash);
    }

    if (! window.BarcodeGlobal.nativeFeedbackListener) {
        window.BarcodeGlobal.nativeFeedbackListener = (event) => {
            const detail = event.detail ?? {};

            if (detail.vibrate) {
                window.BarcodeNative?.vibrate?.();
            }

            if (detail.message) {
                window.BarcodeNative?.toast?.(detail.message, detail.duration ?? 'short');
            }
        };

        window.addEventListener('barcode-native-feedback', window.BarcodeGlobal.nativeFeedbackListener);
    }

    if (! window.BarcodeGlobal.recordLocatedListener) {
        window.BarcodeGlobal.recordLocatedListener = (event) => {
            const recordKey = `${event.detail.targetId}:${event.detail.locatedAt}`;

            if (recordKey === window.BarcodeUiState.lastLocatedRecordKey) {
                return;
            }

            window.BarcodeUiState.lastLocatedRecordKey = recordKey;

            const moveLine = document.getElementById(event.detail.targetId);

            if (! moveLine) {
                return;
            }

            moveLine.scrollIntoView({ behavior: 'smooth', block: 'center' });

            const input = moveLine.querySelector('input[type="number"]');

            if (input) {
                setTimeout(() => {
                    input.focus();
                    input.select();
                }, 250);
            }
        };

        window.addEventListener('barcode-record-located', window.BarcodeGlobal.recordLocatedListener);
    }

    if (! window.BarcodeGlobal.moveLineLocatedListener) {
        window.BarcodeGlobal.moveLineLocatedListener = (event) => {
            window.dispatchEvent(new CustomEvent('barcode-record-located', {
                detail: {
                    targetId: `line-${event.detail.moveLineId}`,
                    locatedAt: event.detail.scannedAt,
                },
            }));
        };

        window.addEventListener('barcode-move-line-located', window.BarcodeGlobal.moveLineLocatedListener);
    }
})();
