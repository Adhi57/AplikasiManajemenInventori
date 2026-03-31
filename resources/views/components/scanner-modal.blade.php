{{-- Scanner Modal --}}
<div id="scanner-modal" class="fixed inset-0 z-50 hidden" aria-modal="true">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeScannerModal()"></div>

    {{-- Modal Content --}}
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative">

            {{-- Header --}}
            <div class="px-5 py-4 bg-red-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-barcode text-white"></i>
                    <h3 class="font-bold text-white text-sm">Scan Barcode</h3>
                </div>
                <button type="button" onclick="closeScannerModal()"
                    class="w-7 h-7 flex items-center justify-center rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Camera Preview --}}
            <div class="p-5">
                <div id="barcode-reader" class="rounded-xl overflow-hidden bg-gray-900 min-h-[250px]"></div>

                {{-- Status --}}
                <div id="scanner-status" class="mt-3 text-center text-sm text-gray-500">
                    <i class="fa-solid fa-camera text-gray-400 mr-1"></i>
                    Arahkan kamera ke barcode produk...
                </div>
            </div>

            {{-- Manual Input Fallback --}}
            <div class="px-5 pb-5">
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-2">
                        Atau masukkan manual
                    </p>
                    <div class="flex gap-2">
                        <input type="text" id="manual-barcode-input" placeholder="Ketik kode barcode..."
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                        <button type="button" onclick="submitManualBarcode()"
                            class="px-4 py-2 bg-red-800 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition">
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let html5QrCode = null;
    let scannerRunning = false;
    let scanCooldown = false;

    function openScannerModal() {
        document.getElementById('scanner-modal').classList.remove('hidden');
        startScanner();
    }

    function closeScannerModal() {
        try {
            stopScanner();
        } catch (e) {
            console.warn('Scanner stop error:', e);
        }
        scanCooldown = false;
        document.getElementById('scanner-modal').classList.add('hidden');
        document.getElementById('manual-barcode-input').value = '';
    }

    function startScanner() {
        const readerDiv = document.getElementById('barcode-reader');
        if (!readerDiv) return;

        try {
            html5QrCode = new Html5Qrcode("barcode-reader");

            html5QrCode.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 150 },
                    aspectRatio: 1.5,
                },
                (decodedText) => {
                    if (!scanCooldown) {
                        onBarcodeScanned(decodedText);
                    }
                },
                (errorMessage) => {
                    // Scan failure — ignore silently
                }
            ).then(() => {
                scannerRunning = true;
            }).catch((err) => {
                scannerRunning = false;
                document.getElementById('scanner-status').innerHTML =
                    '<i class="fa-solid fa-triangle-exclamation text-amber-500 mr-1"></i>' +
                    '<span class="text-amber-600">Kamera tidak tersedia. Gunakan input manual.</span>';
            });
        } catch (e) {
            scannerRunning = false;
            console.warn('Scanner init error:', e);
        }
    }

    function stopScanner() {
        if (html5QrCode) {
            if (scannerRunning) {
                html5QrCode.stop().then(() => {
                    try { html5QrCode.clear(); } catch (e) { }
                }).catch(() => { });
            } else {
                try { html5QrCode.clear(); } catch (e) { }
            }
            scannerRunning = false;
            html5QrCode = null;
        }
    }

    function submitManualBarcode() {
        const input = document.getElementById('manual-barcode-input');
        const code = input.value.trim();
        if (code && !scanCooldown) {
            onBarcodeScanned(code);
        }
    }

    function onBarcodeScanned(code) {
        scanCooldown = true;
        document.dispatchEvent(new CustomEvent('barcode-scanned', { detail: { code: code } }));
        document.getElementById('manual-barcode-input').value = '';

        // Visual cooldown feedback (2 seconds)
        const statusEl = document.getElementById('scanner-status');
        let countdown = 2;
        statusEl.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i>' +
            '<span class="text-emerald-600 font-medium">Berhasil scan: ' + code + '</span>' +
            '<span class="text-gray-400 ml-1">(' + countdown + 's)</span>';

        const interval = setInterval(() => {
            countdown--;
            if (countdown > 0) {
                statusEl.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i>' +
                    '<span class="text-emerald-600 font-medium">Berhasil scan: ' + code + '</span>' +
                    '<span class="text-gray-400 ml-1">(' + countdown + 's)</span>';
            } else {
                clearInterval(interval);
                scanCooldown = false;
                statusEl.innerHTML = '<i class="fa-solid fa-camera text-gray-400 mr-1"></i>' +
                    'Arahkan kamera ke barcode produk...';
            }
        }, 1000);
    }
</script>