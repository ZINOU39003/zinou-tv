<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تجربة البث المباشر - Zinou TV</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-primary: #06080f;
            --bg-secondary: #0c1220;
            --bg-card: rgba(15, 24, 42, 0.75);
            --border-glass: rgba(255, 255, 255, 0.08);
            
            --accent-primary: #00d4aa;
            --accent-primary-hover: #00b894;
            --accent-primary-glow: rgba(0, 212, 170, 0.2);
            
            --accent-gold: #f0b429;
            --accent-gold-hover: #d4941a;
            
            --text-main: #e8f0fe;
            --text-muted: #7b90b8;
            
            --danger: #ff5a7e;
            --success: #00d4aa;
            --warning: #f0b429;
            
            --radius-lg: 16px;
            --radius-md: 10px;
            --radius-sm: 6px;
            
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow-x: hidden;
            background-image: 
                radial-gradient(ellipse at 10% 20%, rgba(0, 212, 170, 0.05) 0%, transparent 60%),
                radial-gradient(ellipse at 90% 80%, rgba(79, 126, 249, 0.05) 0%, transparent 60%);
            padding: 15px;
        }

        .container {
            width: 100%;
            max-width: 780px;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-lg);
            padding: 20px;
            box-shadow: 0 8px 40px -8px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            position: relative;
        }

        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid var(--border-glass);
            padding-bottom: 12px;
        }

        .logo-block {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--accent-primary), #4f7ef9);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 14px;
            color: #06080f;
            box-shadow: 0 0 15px rgba(0, 212, 170, 0.3);
        }

        .logo-text {
            font-size: 16px;
            font-weight: 800;
            background: linear-gradient(90deg, var(--accent-primary), #4f7ef9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .channel-title {
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            text-align: right;
            max-width: 70%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .video-container {
            width: 100%;
            aspect-ratio: 16/9;
            background: #000;
            border-radius: var(--radius-md);
            overflow: hidden;
            position: relative;
            border: 1px solid var(--border-glass);
            box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.8);
        }

        video {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .status-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(6, 8, 15, 0.95);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            text-align: center;
            z-index: 10;
            transition: var(--transition-smooth);
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-loading {
            background: rgba(240, 180, 41, 0.1);
            color: var(--warning);
            border: 1px solid rgba(240, 180, 41, 0.2);
        }

        .status-active {
            background: rgba(0, 212, 170, 0.1);
            color: var(--success);
            border: 1px solid rgba(0, 212, 170, 0.2);
        }

        .status-failed {
            background: rgba(255, 90, 126, 0.15);
            color: var(--danger);
            border: 1px solid rgba(255, 90, 126, 0.3);
        }

        .status-text {
            font-size: 14px;
            color: var(--text-main);
            margin-bottom: 8px;
            max-width: 90%;
            line-height: 1.5;
            font-weight: 600;
        }

        .status-subtext {
            font-size: 12px;
            color: var(--text-muted);
            max-width: 85%;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .controls-row {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition-smooth);
            text-decoration: none;
        }

        .btn-primary {
            background: var(--accent-primary);
            color: #06080f;
        }

        .btn-primary:hover {
            background: var(--accent-primary-hover);
            box-shadow: 0 0 12px var(--accent-primary-glow);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-main);
            border: 1px solid var(--border-glass);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .btn-danger {
            background: rgba(255, 90, 126, 0.15);
            color: var(--danger);
            border: 1px solid rgba(255, 90, 126, 0.3);
        }

        .btn-danger:hover {
            background: rgba(255, 90, 126, 0.25);
        }

        .btn-gold {
            background: rgba(240, 180, 41, 0.15);
            color: var(--warning);
            border: 1px solid rgba(240, 180, 41, 0.3);
        }

        .btn-gold:hover {
            background: rgba(240, 180, 41, 0.25);
        }

        .url-box {
            background: rgba(0,0,0,0.3);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-sm);
            padding: 8px 12px;
            font-size: 11px;
            font-family: monospace;
            color: var(--text-muted);
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .spinner {
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255,255,255,0.1);
            border-top-color: var(--warning);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-bottom: 12px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .d-flex {
            display: flex;
            gap: 8px;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <!-- Header bar -->
            <div class="header-bar">
                <div class="channel-title" id="channelTitle">
                    {{ $name }}
                    <span id="videoQuality" style="margin-right: 8px; font-size: 11px; background: rgba(0, 212, 170, 0.15); color: var(--accent-primary); padding: 3px 8px; border-radius: 4px; border: 1px solid rgba(0, 212, 170, 0.25); display: none;"></span>
                </div>
                <div class="logo-block">
                    <span class="logo-text">مشغل تجربة البث</span>
                    @if(!empty($logo))
                        <img src="{{ $logo }}" alt="Logo" style="height: 32px; width: 32px; object-fit: contain; border-radius: 6px; background: rgba(255,255,255,0.08); padding: 3px; border: 1px solid var(--border-glass);">
                    @else
                        <div class="logo-icon">Z</div>
                    @endif
                </div>
            </div>

            <!-- Video Player -->
            <div class="video-container">
                <video id="testVideo" controls autoplay playsinline></video>
                
                <!-- Status Overlay (controlled by js) -->
                <div id="statusOverlay" class="status-overlay">
                    <div id="statusIndicator">
                        <div class="spinner"></div>
                        <div class="status-badge status-loading">⏳ جاري تحميل البث...</div>
                    </div>
                    <div class="status-text" id="statusText">نقوم الآن بالاتصال برابط البث وتهيئته...</div>
                    <div class="status-subtext" id="statusSubtext">قد يستغرق ذلك بضع ثوانٍ تبعاً لسرعة استجابة خادم البث.</div>
                    
                    <!-- Quick Bypass Action (shown only on error) -->
                    <div id="bypassContainer" style="display: none; margin-top: 15px;">
                        <a href="{{ $url }}" target="_blank" class="btn btn-gold" id="btnBypass">
                            🔓 فك حجب الرابط (افتح في نافذة جديدة)
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stream Link Copy box -->
            <div class="url-box">
                <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; direction: ltr;" id="streamUrlText">{{ $url }}</span>
                <button type="button" class="btn btn-secondary" style="padding: 4px 8px; font-size: 10px;" onclick="copyStreamUrl()">📋 نسخ</button>
            </div>

            <!-- Bottom Controls -->
            <div class="controls-row">
                <button type="button" class="btn btn-danger" onclick="window.close()">❌ إغلاق النافذة</button>
                <div class="d-flex">
                    <button type="button" class="btn btn-secondary" onclick="reloadPlayer()">🔄 إعادة التحميل</button>
                </div>
            </div>
        </div>
    </div>

    <!-- HLS & DASH player CDNs -->
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.4.12/dist/hls.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dashjs@4.7.1/dist/dash.all.min.js"></script>

    <script>
        const url = "{{ $url }}";
        const proxyUrl = "{{ route('admin.har.stream-proxy') }}?url=" + encodeURIComponent(url);
        const video = document.getElementById('testVideo');
        const statusOverlay = document.getElementById('statusOverlay');
        const statusIndicator = document.getElementById('statusIndicator');
        const statusText = document.getElementById('statusText');
        const statusSubtext = document.getElementById('statusSubtext');
        const bypassContainer = document.getElementById('bypassContainer');
        
        let hlsInstance = null;
        let dashPlayer = null;
        let hasPlayedSuccessfully = false;
        let isUsingProxy = false;

        // Initialize playback
        function initPlayer(useProxy = false) {
            resetPlayer();
            hasPlayedSuccessfully = false;
            isUsingProxy = useProxy;
            
            const targetUrl = useProxy ? proxyUrl : url;
            const modeText = useProxy ? 'عبر البروكسي المحلي (تخطي CORS)' : 'مباشرة من المصدر (مع كوكيز المتصفح)';
            
            showStatus('loading', '⏳ جاري الاتصال بالبث المباشر...', `نقوم بالاتصال بالبث ${modeText}...`);
            
            // Setup Native video events to verify if it works
            video.onloadedmetadata = function() {
                updateQualityLabel();
            };

            video.onplaying = function() {
                hasPlayedSuccessfully = true;
                updateQualityLabel();
                showStatus('active', '🟢 القناة تعمل بنجاح!', 'تم بدء تشغيل الفيديو واستقبال البث بنجاح.');
                statusOverlay.style.background = 'rgba(6, 8, 15, 0)';
                // Hide overlay after a brief delay
                setTimeout(() => {
                    if (hasPlayedSuccessfully) {
                        statusOverlay.style.display = 'none';
                    }
                }, 1500);
            };

            video.onerror = function(e) {
                if (!hasPlayedSuccessfully) {
                    if (!isUsingProxy) {
                        console.log("Direct playback failed. Falling back to proxy...");
                        initPlayer(true);
                    } else {
                        handlePlaybackError('فشل تشغيل الرابط في مشغل HTML5 الافتراضي.');
                    }
                }
            };

            setTimeout(() => {
                try {
                    if (url.toLowerCase().includes('.m3u8')) {
                        // Play via Hls.js
                        if (Hls.isSupported()) {
                            hlsInstance = new Hls({
                                maxMaxBufferLength: 10,
                                enableWorker: true,
                                lowLatencyMode: true
                            });
                            hlsInstance.loadSource(targetUrl);
                            hlsInstance.attachMedia(video);
                            
                            hlsInstance.on(Hls.Events.MANIFEST_PARSED, function() {
                                video.play().catch(e => {
                                    showStatus('active', '🟢 تم تحميل البث', 'اضغط تشغيل على مشغل الفيديو لبدء البث.');
                                });
                            });
                            
                            hlsInstance.on(Hls.Events.ERROR, function(event, data) {
                                console.warn('HLS.js Event Error:', data);
                                if (data.fatal && !hasPlayedSuccessfully) {
                                    if (!isUsingProxy) {
                                        console.log("HLS.js direct playback error. Falling back to proxy...");
                                        initPlayer(true);
                                    } else {
                                        switch (data.type) {
                                            case Hls.ErrorTypes.NETWORK_ERROR:
                                                handlePlaybackError('فشل الاتصال بالشبكة (Network Error). قد يكون الرابط منتهياً أو محمياً بـ Cloudflare.');
                                                break;
                                            case Hls.ErrorTypes.MEDIA_ERROR:
                                                hlsInstance.recoverMediaError();
                                                break;
                                            default:
                                                handlePlaybackError('حدث خطأ أثناء تشغيل بث HLS.');
                                                break;
                                        }
                                    }
                                }
                            });
                        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                            // Safari native playback fallback
                            video.src = targetUrl;
                        } else {
                            handlePlaybackError('متصفحك الحالي لا يدعم تقنية تشغيل بث HLS (.m3u8).');
                        }
                    } else if (url.toLowerCase().includes('.mpd')) {
                        // Play via dash.js
                        dashPlayer = dashjs.MediaPlayer().create();
                        dashPlayer.initialize(video, targetUrl, true);
                        
                        dashPlayer.on(dashjs.MediaPlayer.events.ERROR, function(e) {
                            if (!isUsingProxy) {
                                console.log("DASH direct playback error. Falling back to proxy...");
                                initPlayer(true);
                            } else {
                                handlePlaybackError('فشل تشغيل البث بصيغة DASH (.mpd). قد يكون البث منتهياً أو محمياً بـ DRM.');
                            }
                        });
                    } else {
                        // Direct file stream (e.g. .ts or other formats)
                        video.src = targetUrl;
                        video.play().catch(e => {
                            if (!isUsingProxy) {
                                console.log("Direct file playback error. Falling back to proxy...");
                                initPlayer(true);
                            } else {
                                if (url.toLowerCase().includes('.ts')) {
                                    handlePlaybackError('بث MPEG-TS (.ts) لا يمكن تشغيله مباشرة في بعض المتصفحات بدون إضافات.');
                                } else {
                                    handlePlaybackError('فشل بدء التشغيل التلقائي.');
                                }
                            }
                        });
                    }
                } catch (error) {
                    if (!isUsingProxy) {
                        initPlayer(true);
                    } else {
                        handlePlaybackError('حدث خطأ غير متوقع أثناء تهيئة المشغل: ' + error.message);
                    }
                }
            }, 300);
        }

        function handlePlaybackError(message) {
            showStatus(
                'failed', 
                '🔴 لم يشتغل البث (محمي أو منتهي)', 
                message + '<br>يرجى فتح الرابط مباشرة في تبويب جديد لتخطي جدار الحماية (Cloudflare) أو التحقق من صلاحية الحساب.'
            );
            bypassContainer.style.display = 'block';
        }

        function showStatus(type, badgeText, text) {
            statusOverlay.style.display = 'flex';
            statusOverlay.style.background = 'rgba(6, 8, 15, 0.95)';
            
            let badgeClass = '';
            let html = '';
            
            if (type === 'loading') {
                badgeClass = 'status-loading';
                html = `<div class="spinner"></div>`;
                bypassContainer.style.display = 'none';
            } else if (type === 'active') {
                badgeClass = 'status-active';
                html = `🎉 `;
                bypassContainer.style.display = 'none';
            } else if (type === 'failed') {
                badgeClass = 'status-failed';
                html = `⚠️ `;
            }
            
            statusIndicator.innerHTML = `${html}<div class="status-badge ${badgeClass}">${badgeText}</div>`;
            statusText.innerHTML = text;
        }

        function updateQualityLabel() {
            const width = video.videoWidth;
            const height = video.videoHeight;
            if (width > 0 && height > 0) {
                let label = '';
                if (height >= 2160 || width >= 3840) {
                    label = `⚡ 4K UHD (${width}x${height})`;
                } else if (height >= 1440 || width >= 2560) {
                    label = `💎 2K QHD 1440p (${width}x${height})`;
                } else if (height >= 1080 || width >= 1920) {
                    label = `💎 FHD 1080p (${width}x${height})`;
                } else if (height >= 720 || width >= 1280) {
                    label = `🎬 HD 720p (${width}x${height})`;
                } else if (height >= 576 || width >= 1024) {
                    label = `📺 SD 576p (${width}x${height})`;
                } else if (height >= 480 || width >= 854) {
                    label = `📺 SD 480p (${width}x${height})`;
                } else {
                    label = `📺 SD ${height}p (${width}x${height})`;
                }
                
                const qBadge = document.getElementById('videoQuality');
                if (qBadge) {
                    qBadge.innerText = label;
                    qBadge.style.display = 'inline-block';
                }
            }
        }

        function resetPlayer() {
            const qBadge = document.getElementById('videoQuality');
            if (qBadge) {
                qBadge.style.display = 'none';
                qBadge.innerText = '';
            }
            if (hlsInstance) {
                hlsInstance.destroy();
                hlsInstance = null;
            }
            if (dashPlayer) {
                dashPlayer.reset();
                dashPlayer = null;
            }
            video.src = '';
            video.load();
        }

        function reloadPlayer() {
            initPlayer(false); // Reset to try direct first on manual reload
        }

        function copyStreamUrl() {
            const tempInput = document.createElement('input');
            tempInput.value = url;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            alert('تم نسخ رابط البث إلى الحافظة!');
        }

        // Run when page loads
        window.addEventListener('DOMContentLoaded', () => {
            initPlayer();
        });

        // Clean up resources on window close
        window.addEventListener('beforeunload', () => {
            resetPlayer();
        });
    </script>
</body>
</html>
