/**
 * Canvas de signature réutilisable + chargement depuis le profil utilisateur.
 */
(function (global) {
    'use strict';

    function profileSignatureUrl() {
        const meta = document.querySelector('meta[name="profile-signature-url"]');
        return meta ? meta.getAttribute('content') : null;
    }

    function csrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function pos(canvas, ev) {
        const r = canvas.getBoundingClientRect();
        const scaleX = canvas.width / r.width;
        const scaleY = canvas.height / r.height;
        const clientX = ev.touches ? ev.touches[0].clientX : ev.clientX;
        const clientY = ev.touches ? ev.touches[0].clientY : ev.clientY;
        return {
            x: (clientX - r.left) * scaleX,
            y: (clientY - r.top) * scaleY,
        };
    }

    function isCanvasBlank(canvas) {
        const blank = document.createElement('canvas');
        blank.width = canvas.width;
        blank.height = canvas.height;
        return canvas.toDataURL() === blank.toDataURL();
    }

    function drawDataUrlOnCanvas(canvas, dataUrl, hiddenInput) {
        const ctx = canvas.getContext('2d');
        const img = new Image();
        img.onload = function () {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            const scale = Math.min(canvas.width / img.width, canvas.height / img.height);
            const w = img.width * scale;
            const h = img.height * scale;
            const x = (canvas.width - w) / 2;
            const y = (canvas.height - h) / 2;
            ctx.drawImage(img, x, y, w, h);
            if (hiddenInput) {
                hiddenInput.value = canvas.toDataURL('image/png');
            }
        };
        img.onerror = function () {
            alert('Impossible d\'afficher la signature.');
        };
        img.src = dataUrl;
    }

    function initPad(root) {
        const canvasId = root.dataset.canvasId;
        const hiddenId = root.dataset.hiddenId;
        const formId = root.dataset.formId || '';

        const canvas = document.getElementById(canvasId);
        const hiddenInput = document.getElementById(hiddenId);
        if (!canvas || !hiddenInput) {
            return;
        }

        const ctx = canvas.getContext('2d');
        let drawing = false;

        function start(ev) {
            drawing = true;
            ctx.beginPath();
            const p = pos(canvas, ev);
            ctx.moveTo(p.x, p.y);
            ev.preventDefault();
        }

        function move(ev) {
            if (!drawing) return;
            const p = pos(canvas, ev);
            ctx.strokeStyle = '#111';
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.lineTo(p.x, p.y);
            ctx.stroke();
            ev.preventDefault();
        }

        function end() {
            drawing = false;
        }

        canvas.addEventListener('mousedown', start);
        canvas.addEventListener('mousemove', move);
        window.addEventListener('mouseup', end);
        canvas.addEventListener('touchstart', start, { passive: false });
        canvas.addEventListener('touchmove', move, { passive: false });
        canvas.addEventListener('touchend', end);

        const clearBtn = root.querySelector('[data-signature-clear]');
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                hiddenInput.value = '';
            });
        }

        const loadBtn = root.querySelector('[data-signature-load-profile]');
        if (loadBtn) {
            loadBtn.addEventListener('click', function () {
                CofinaSignature.loadProfileOntoCanvas(canvasId, hiddenId, loadBtn);
            });
        }

        if (formId) {
            const form = document.getElementById(formId);
            form?.addEventListener('submit', function () {
                if (!isCanvasBlank(canvas)) {
                    hiddenInput.value = canvas.toDataURL('image/png');
                }
            });
        }
    }

    function initAllPads() {
        document.querySelectorAll('[data-cofina-signature-pad]').forEach(initPad);
    }

    const CofinaSignature = {
        initAllPads,

        loadProfileOntoCanvas(canvasId, hiddenId, buttonEl) {
            const url = profileSignatureUrl();
            if (!url) {
                alert('Configuration signature profil indisponible.');
                return Promise.resolve(false);
            }

            const canvas = document.getElementById(canvasId);
            const hiddenInput = hiddenId ? document.getElementById(hiddenId) : null;
            if (!canvas) {
                return Promise.resolve(false);
            }

            if (buttonEl) {
                buttonEl.disabled = true;
            }

            return fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            })
                .then(function (res) {
                    if (!res.ok) {
                        throw new Error('fetch_failed');
                    }
                    return res.json();
                })
                .then(function (data) {
                    if (!data.has_signature || !data.data_url) {
                        alert('Aucune signature enregistrée sur votre profil. Rendez-vous dans Mon profil pour en ajouter une.');
                        return false;
                    }
                    drawDataUrlOnCanvas(canvas, data.data_url, hiddenInput);
                    return true;
                })
                .catch(function () {
                    alert('Impossible de charger votre signature de profil.');
                    return false;
                })
                .finally(function () {
                    if (buttonEl) {
                        buttonEl.disabled = false;
                    }
                });
        },

        loadProfileOntoSignaturePad(signaturePad, hiddenInput) {
            const url = profileSignatureUrl();
            if (!url || !signaturePad) {
                alert('Configuration signature profil indisponible.');
                return Promise.resolve(false);
            }

            return fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            })
                .then(function (res) {
                    if (!res.ok) {
                        throw new Error('fetch_failed');
                    }
                    return res.json();
                })
                .then(function (data) {
                    if (!data.has_signature || !data.data_url) {
                        alert('Aucune signature enregistrée sur votre profil. Rendez-vous dans Mon profil pour en ajouter une.');
                        return false;
                    }
                    signaturePad.fromDataURL(data.data_url, {
                        ratio: Math.max(window.devicePixelRatio || 1, 1),
                    });
                    if (hiddenInput) {
                        hiddenInput.value = signaturePad.toDataURL('image/png');
                    }
                    return true;
                })
                .catch(function () {
                    alert('Impossible de charger votre signature de profil.');
                    return false;
                });
        },

        flushCanvas(canvasId, hiddenId) {
            const canvas = document.getElementById(canvasId);
            const hid = document.getElementById(hiddenId);
            if (canvas && hid && canvas.getContext && !isCanvasBlank(canvas)) {
                hid.value = canvas.toDataURL('image/png');
            }
        },
    };

    global.CofinaSignature = CofinaSignature;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAllPads);
    } else {
        initAllPads();
    }
})(window);
