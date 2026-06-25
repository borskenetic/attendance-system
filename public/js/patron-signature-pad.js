(function (global) {
    'use strict';

    const LIB = 'https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js';
    const DEFAULT_HEIGHT = 150;
    let loading = null;

    function loadSignaturePad() {
        if (global.SignaturePad) {
            return Promise.resolve();
        }

        if (loading) {
            return loading;
        }

        loading = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = LIB;
            script.onload = () => resolve();
            script.onerror = () => reject(new Error('Failed to load SignaturePad'));
            document.head.appendChild(script);
        });

        return loading;
    }

    function displaySize(canvas) {
        const rect = canvas.getBoundingClientRect();
        const width = rect.width > 0 ? rect.width : canvas.offsetWidth;
        const height = rect.height > 0 ? rect.height : canvas.offsetHeight || DEFAULT_HEIGHT;

        return { width, height };
    }

    function initCanvas(canvas) {
        const inputId = canvas.dataset.signatureInput;
        const clearId = canvas.dataset.signatureClear;
        const input = inputId ? document.getElementById(inputId) : null;
        const clearBtn = clearId ? document.getElementById(clearId) : null;
        const form = canvas.closest('form');

        if (!canvas._patronSignaturePad) {
            canvas._patronSignaturePad = new global.SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)',
            });

            clearBtn?.addEventListener('click', () => {
                canvas._patronSignaturePad.clear();
                if (input) {
                    input.value = '';
                }
            });

            form?.addEventListener('submit', () => {
                if (!canvas._patronSignaturePad.isEmpty() && input) {
                    input.value = canvas._patronSignaturePad.toDataURL();
                }
            });
        }

        const signaturePad = canvas._patronSignaturePad;

        function resizeCanvas() {
            const { width, height } = displaySize(canvas);

            if (width < 1 || height < 1) {
                return;
            }

            const ratio = Math.max(global.devicePixelRatio || 1, 1);
            const data = signaturePad.isEmpty() ? null : signaturePad.toData();

            canvas.width = Math.floor(width * ratio);
            canvas.height = Math.floor(height * ratio);

            const ctx = canvas.getContext('2d');
            ctx.setTransform(ratio, 0, 0, ratio, 0, 0);

            canvas.style.width = `${width}px`;
            canvas.style.height = `${height}px`;

            signaturePad.clear();
            if (data) {
                signaturePad.fromData(data);
            }
        }

        canvas._patronSignaturePadResize = resizeCanvas;
        resizeCanvas();

        if (!canvas._patronSignaturePadObserver && global.ResizeObserver) {
            canvas._patronSignaturePadObserver = new ResizeObserver(() => resizeCanvas());
            canvas._patronSignaturePadObserver.observe(canvas);
        }
    }

    function initPatronSignaturePads(root) {
        const scope = root || document;

        loadSignaturePad()
            .then(() => {
                scope.querySelectorAll('[data-signature-pad]').forEach(initCanvas);
            })
            .catch(() => {});
    }

    global.initPatronSignaturePads = initPatronSignaturePads;

    document.addEventListener('DOMContentLoaded', () => initPatronSignaturePads());
    document.addEventListener('turbo:load', () => initPatronSignaturePads());
})(window);
