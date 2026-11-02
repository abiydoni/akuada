/**
 * AkuAda Realtime Notification & Sound Engine
 * - Web Audio API synthetic chime (zero external dependencies)
 * - Browser Desktop Notifications + In-App Floating Toasts
 * - Live Polling & Unread Badge Synchronizer for Admin, Vendor, and Customer
 */

(function(window) {
    'use strict';

    // 1. Web Audio API Chime Synthesizer
    const AkuAdaAudio = {
        ctx: null,
        isMuted: function() {
            return localStorage.getItem('akuada_sound_muted') === '1';
        },
        toggleMute: function() {
            const current = this.isMuted();
            localStorage.setItem('akuada_sound_muted', current ? '0' : '1');
            return !current;
        },
        initContext: function() {
            if (!this.ctx) {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (AudioCtx) {
                    this.ctx = new AudioCtx();
                }
            }
            if (this.ctx && this.ctx.state === 'suspended') {
                this.ctx.resume().catch(function() {});
            }
        },
        playChime: function(type = 'chat') {
            if (this.isMuted()) return;
            try {
                this.initContext();
                if (!this.ctx) return;

                const now = this.ctx.currentTime;
                const gainNode = this.ctx.createGain();
                gainNode.connect(this.ctx.destination);

                if (type === 'chat') {
                    // Modern 2-tone chime: D5 (587Hz) -> A5 (880Hz)
                    const osc1 = this.ctx.createOscillator();
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(587.33, now);
                    osc1.connect(gainNode);

                    const osc2 = this.ctx.createOscillator();
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(880.00, now + 0.10);
                    osc2.connect(gainNode);

                    gainNode.gain.setValueAtTime(0.0001, now);
                    gainNode.gain.exponentialRampToValueAtTime(0.35, now + 0.02);
                    gainNode.gain.exponentialRampToValueAtTime(0.18, now + 0.10);
                    gainNode.gain.exponentialRampToValueAtTime(0.40, now + 0.12);
                    gainNode.gain.exponentialRampToValueAtTime(0.0001, now + 0.45);

                    osc1.start(now);
                    osc1.stop(now + 0.12);
                    osc2.start(now + 0.10);
                    osc2.stop(now + 0.50);
                } else if (type === 'order') {
                    // Joyful 3-tone notification: C5 (523Hz) -> E5 (659Hz) -> G5 (784Hz)
                    [523.25, 659.25, 783.99].forEach(function(freq, idx) {
                        const osc = AkuAdaAudio.ctx.createOscillator();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(freq, now + (idx * 0.12));
                        osc.connect(gainNode);
                        osc.start(now + (idx * 0.12));
                        osc.stop(now + (idx * 0.12) + 0.35);
                    });

                    gainNode.gain.setValueAtTime(0.0001, now);
                    gainNode.gain.exponentialRampToValueAtTime(0.40, now + 0.05);
                    gainNode.gain.exponentialRampToValueAtTime(0.0001, now + 0.75);
                }
            } catch (e) {
                console.warn('Audio chime error:', e);
            }
        }
    };

    // Unlock Web Audio on first user interaction
    ['click', 'touchstart', 'keydown'].forEach(function(evt) {
        document.addEventListener(evt, function() {
            AkuAdaAudio.initContext();
        }, { once: true, passive: true });
    });

    // 2. In-App Toast & Desktop Notification Manager
    const AkuAdaNotify = {
        toastContainer: null,
        initContainer: function() {
            if (!this.toastContainer) {
                let c = document.getElementById('akuada-toast-container');
                if (!c) {
                    c = document.createElement('div');
                    c.id = 'akuada-toast-container';
                    c.className = 'fixed top-5 right-5 z-[9999] flex flex-col gap-2.5 max-w-sm w-full pointer-events-none select-none font-sans';
                    document.body.appendChild(c);
                }
                this.toastContainer = c;
            }
        },
        requestPermission: function() {
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }
        },
        showDesktop: function(title, body, url) {
            if ('Notification' in window && Notification.permission === 'granted' && document.hidden) {
                try {
                    const n = new Notification(title, {
                        body: body,
                        icon: '/favicon.ico',
                        tag: 'akuada-notify'
                    });
                    n.onclick = function() {
                        window.focus();
                        if (url) window.location.href = url;
                        n.close();
                    };
                } catch (e) {}
            }
        },
        showToast: function(title, body, url, type = 'chat') {
            this.initContainer();

            const toast = document.createElement('div');
            toast.className = 'pointer-events-auto transform translate-y-[-15px] opacity-0 transition-all duration-300 ease-out bg-white rounded-2xl p-4 shadow-2xl border flex items-start gap-3 backdrop-blur-sm ' + 
                (type === 'order' ? 'border-blue-400 ring-2 ring-blue-100' : 'border-purple-400 ring-2 ring-purple-100');

            const iconName = type === 'order' ? 'receipt_long' : 'forum';
            const iconBg = type === 'order' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700';

            toast.innerHTML = `
                <div class="w-10 h-10 rounded-xl ${iconBg} flex items-center justify-center flex-shrink-0 font-bold shadow-xs">
                    <span class="material-symbols-outlined text-[20px]">${iconName}</span>
                </div>
                <div class="flex-1 min-w-0 pr-2">
                    <div class="flex items-center justify-between gap-1">
                        <strong class="text-xs font-black text-slate-900 truncate">${title}</strong>
                        <span class="text-[9px] font-mono text-slate-400">Baru saja</span>
                    </div>
                    <p class="text-[11px] text-slate-600 line-clamp-2 mt-0.5 leading-snug">${body}</p>
                    ${url ? `<a href="${url}" class="inline-flex items-center gap-1 text-[11px] font-bold ${type === 'order' ? 'text-blue-700 hover:text-blue-900' : 'text-purple-700 hover:text-purple-900'} mt-1.5 hover:underline">Lihat Detail ↗</a>` : ''}
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-600 p-1 flex-shrink-0" onclick="this.parentElement.remove()">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            `;

            if (url) {
                toast.style.cursor = 'pointer';
                toast.onclick = function(e) {
                    if (e.target.tagName !== 'BUTTON' && !e.target.closest('button')) {
                        window.location.href = url;
                    }
                };
            }

            this.toastContainer.prepend(toast);

            // Animate in
            requestAnimationFrame(function() {
                toast.classList.remove('translate-y-[-15px]', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            });

            // Auto dismiss after 7 seconds
            setTimeout(function() {
                toast.classList.remove('opacity-100', 'translate-y-0');
                toast.classList.add('opacity-0', 'translate-y-[-10px]');
                setTimeout(function() { toast.remove(); }, 350);
            }, 7000);
        }
    };

    // 3. Realtime Engine & Polling Synchronizer
    const AkuAdaRealtime = {
        pollTimer: null,
        intervalMs: 3500,
        lastSeenMessageId: 0,
        lastSeenOrderId: 0,
        isFirstRun: true,
        originalTitle: document.title,

        init: function() {
            // Restore last seen IDs from session to prevent spamming notification on fresh page navigation
            this.lastSeenMessageId = parseInt(sessionStorage.getItem('akuada_last_msg_id') || '0', 10);
            this.lastSeenOrderId   = parseInt(sessionStorage.getItem('akuada_last_order_id') || '0', 10);

            // Ask notification permission on first interaction
            document.addEventListener('click', function() {
                AkuAdaNotify.requestPermission();
            }, { once: true });

            // Start loop
            this.check();
            this.pollTimer = setInterval(this.check.bind(this), this.intervalMs);
        },

        check: function() {
            const self = this;
            fetch('/api/realtime-check', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(res) {
                if (!res.ok) throw new Error('Status ' + res.status);
                return res.json();
            })
            .then(function(data) {
                if (data.status === 'success') {
                    self.handlePayload(data);
                }
            })
            .catch(function(err) {
                // Silently keep polling
            });
        },

        handlePayload: function(data) {
            const unreadCount = parseInt(data.unread_chat_count || 0, 10);
            const latestMsg   = data.latest_message;
            const latestOrder = data.latest_order;
            const role        = data.role || 'guest';

            // 1. Update Badges everywhere
            this.updateBadges(unreadCount);

            // 2. Handle New Incoming Chat Message
            if (latestMsg && latestMsg.id) {
                if (this.lastSeenMessageId > 0 && latestMsg.id > this.lastSeenMessageId) {
                    // New message arrived!
                    AkuAdaAudio.playChime('chat');

                    const chatUrl = (role === 'admin') ? '/admin/chat?key=' + encodeURIComponent(latestMsg.conversation_key)
                                : ((role === 'vendor') ? '/vendor/chat?key=' + encodeURIComponent(latestMsg.conversation_key)
                                : '/customer/chat?key=' + encodeURIComponent(latestMsg.conversation_key));

                    const senderTitle = 'Pesan Baru dari ' + (latestMsg.sender_company_name || latestMsg.sender_name);
                    const snippet = (latestMsg.message || '').replace(/<[^>]*>?/gm, '');

                    AkuAdaNotify.showToast(senderTitle, snippet, chatUrl, 'chat');
                    AkuAdaNotify.showDesktop(senderTitle, snippet, chatUrl);

                    // If user is currently on chat page, trigger soft reload of messages!
                    if (typeof window.loadMessages === 'function') {
                        window.loadMessages(false);
                    }
                }
                this.lastSeenMessageId = Math.max(this.lastSeenMessageId, latestMsg.id);
                sessionStorage.setItem('akuada_last_msg_id', this.lastSeenMessageId);
            }

            // 3. Handle New Order / PO Creation
            if (latestOrder && latestOrder.id) {
                if (this.lastSeenOrderId > 0 && latestOrder.id > this.lastSeenOrderId) {
                    AkuAdaAudio.playChime('order');

                    let orderUrl = '/';
                    let orderTitle = 'Pesanan Purchase Order (PO) Baru!';
                    let orderDesc = 'PO #' + (latestOrder.po_number || latestOrder.order_number) + ' diterbitkan sebesar Rp ' + (latestOrder.total_amount || 0).toLocaleString('id-ID');

                    if (role === 'admin') {
                        orderUrl = '/admin/transactions';
                        orderTitle = '📢 PO Baru Masuk #' + (latestOrder.po_number || latestOrder.order_number);
                        orderDesc = (latestOrder.buyer_name || 'Buyer') + ' menerbitkan PO ke ' + (latestOrder.vendor_name || 'Vendor');
                    } else if (role === 'vendor') {
                        orderUrl = '/vendor/orders/' + latestOrder.id;
                        orderTitle = '🚚 PO Masuk Baru untuk Perusahaan Anda!';
                        orderDesc = 'PO #' + (latestOrder.po_number || latestOrder.order_number) + ' siap diproses & disiapkan gudang.';
                    } else if (role === 'customer') {
                        orderUrl = '/customer/orders/' + latestOrder.id;
                        orderTitle = '✅ PO Berhasil Diterbitkan #' + (latestOrder.po_number || latestOrder.order_number);
                    }

                    AkuAdaNotify.showToast(orderTitle, orderDesc, orderUrl, 'order');
                    AkuAdaNotify.showDesktop(orderTitle, orderDesc, orderUrl);

                    // If currently on orders/transactions page, show live-update banner
                    self.showPageReloadBanner(orderTitle, orderUrl);
                }
                this.lastSeenOrderId = Math.max(this.lastSeenOrderId, latestOrder.id);
                sessionStorage.setItem('akuada_last_order_id', this.lastSeenOrderId);
            }

            this.isFirstRun = false;
        },

        updateBadges: function(unreadCount) {
            // Update document title
            if (unreadCount > 0) {
                document.title = '(' + unreadCount + ') ' + this.originalTitle;
            } else {
                document.title = this.originalTitle;
            }

            // Global Navbar badges
            const badges = document.querySelectorAll('.chat-unread-badge, #globalChatUnreadBadge, #sidebarChatUnreadBadge, #floatingUnreadBadge');
            badges.forEach(function(b) {
                if (unreadCount > 0) {
                    b.textContent = unreadCount > 99 ? '99+' : unreadCount;
                    b.classList.remove('hidden');
                    b.style.display = 'inline-flex';
                } else {
                    b.textContent = '0';
                    b.classList.add('hidden');
                    b.style.display = 'none';
                }
            });
        },

        showPageReloadBanner: function(title, url) {
            const currentPath = window.location.pathname;
            const isOrderPage = currentPath.includes('/admin/transactions') || currentPath.includes('/vendor/orders') || currentPath.includes('/customer/orders') || currentPath.includes('/dashboard');

            if (isOrderPage && !document.getElementById('live-reload-banner')) {
                const banner = document.createElement('div');
                banner.id = 'live-reload-banner';
                banner.className = 'fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50 bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-2xl border border-purple-500/80 flex items-center gap-3 animate-bounce';
                banner.innerHTML = `
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                    <span class="text-xs font-bold">${title}</span>
                    <button type="button" onclick="window.location.reload()" class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-extrabold shadow-sm">
                        Muat Ulang Halaman ↻
                    </button>
                    <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white text-xs ml-1">✕</button>
                `;
                document.body.appendChild(banner);
            }
        }
    };

    // Expose globally
    window.AkuAdaAudio = AkuAdaAudio;
    window.AkuAdaNotify = AkuAdaNotify;
    window.AkuAdaRealtime = AkuAdaRealtime;

    // Auto boot on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() { AkuAdaRealtime.init(); });
    } else {
        AkuAdaRealtime.init();
    }

})(window);

