@extends('admin.layouts.app')

@section('header_title', 'نتائج تحليل ملف حركة الشبكة')
@section('header_subtitle', 'تم العثور على روابط البث التالية. يمكنك فحص حالة تشغيل الروابط وتجربتها وتوزيعها تلقائياً.')

@section('content')
<div class="row" style="width: 100%;">
    <div class="col-md-12" style="width: 100%;">
        <form action="{{ route('admin.har.distribute') }}" method="POST">
            @csrf

            <div class="card">
                <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                    <h2>
                        <span class="icon">✨</span>
                        روابط البث المكتشفة ({{ count($streams) }})
                    </h2>
                    
                    <div class="d-flex gap-2" style="display: flex; gap: 10px;">
                        <button type="button" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;" onclick="downloadTextFile()">
                            📥 تحميل كملف نصي (.txt)
                        </button>
                        <button type="submit" class="btn btn-primary" style="padding: 8px 16px; font-size: 13px;">
                            ⚡ توزيع الروابط على قنوات الموقع
                        </button>
                    </div>
                </div>

                <div class="table-container mt-3" style="width: 100%; overflow-x: auto; margin-top: 15px;">
                    <table class="table" style="width: 100%; border-collapse: collapse; text-align: right;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--border-glass);">
                                <th style="padding: 12px; text-align: right; color: var(--text-muted); font-size: 11px; width: 90px;">نوع البث</th>
                                <th style="padding: 12px; text-align: right; color: var(--text-muted); font-size: 11px; width: 160px;">الاسم المخمن / المكتشف</th>
                                <th style="padding: 12px; text-align: center; color: var(--text-muted); font-size: 11px; width: 110px;">تجربة القناة</th>
                                <th style="padding: 12px; text-align: center; color: var(--text-muted); font-size: 11px; width: 110px;">حالة البث</th>
                                <th style="padding: 12px; text-align: center; color: var(--text-muted); font-size: 11px; width: 150px;">تطابق قاعدة البيانات</th>
                                <th style="padding: 12px; text-align: right; color: var(--text-muted); font-size: 11px;">رابط البث الحقيقي المستخرج</th>
                                <th style="padding: 12px; text-align: center; color: var(--text-muted); font-size: 11px; width: 280px;">التوزيع لقناة في الموقع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($streams as $index => $stream)
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <!-- Stream Type -->
                                    <td style="padding: 12px; vertical-align: middle;">
                                        <span class="badge badge-info" style="font-size: 10px;">{{ $stream['type'] }}</span>
                                    </td>
                                    
                                    <!-- Guessed / Discovered Name -->
                                    <td style="padding: 12px; vertical-align: middle; font-weight: 700; color: #fff; font-size: 13px;" class="channel-name-cell">
                                        {{ $stream['guessed_name'] }}
                                    </td>

                                    <!-- Video Player Test Trigger -->
                                    <td style="padding: 12px; vertical-align: middle; text-align: center;">
                                        <button type="button" class="btn btn-xs btn-gold btn-play-stream" data-url="{{ $stream['url'] }}" data-name="{{ $stream['guessed_name'] }}" data-type="{{ $stream['type'] }}" style="padding: 5px 10px; font-size: 11px; font-weight: 800; border-radius: 4px; cursor: pointer; text-shadow: none;">
                                            📺 تجربة القناة
                                        </button>
                                    </td>
                                    
                                    <!-- Link Status (AJAX checked) -->
                                    <td style="padding: 12px; vertical-align: middle; text-align: center;" class="link-status-cell" data-url="{{ $stream['url'] }}">
                                        <span class="badge badge-warning status-badge" style="font-size: 10px; background: rgba(240,180,41,0.12); color: var(--warning);">⏳ جاري...</span>
                                    </td>
                                    
                                    <!-- DB Match Type -->
                                    <td style="padding: 12px; vertical-align: middle; text-align: center;">
                                        @if($stream['match_type'] === 'exact')
                                            <span class="badge badge-success" style="font-size: 10px; background: rgba(0,212,170,0.15); color: var(--success);" title="نفس الرابط مسجل لهذه القناة بالكامل">🟢 مطابق تماماً</span>
                                        @elseif($stream['match_type'] === 'sig')
                                            <span class="badge badge-warning" style="font-size: 10px; background: rgba(240,180,41,0.15); color: var(--warning);" title="رابط جديد لنفس القناة المحددة">🟡 قناة متطابقة</span>
                                        @else
                                            <span class="badge badge-info" style="font-size: 10px; background: rgba(79,126,249,0.15); color: var(--accent-secondary);" title="لم يتم العثور على قناة مسجلة بهذا المعرف">⚪ رابط جديد</span>
                                        @endif
                                    </td>
                                    
                                    <!-- Extracted URL -->
                                    <td style="padding: 12px; vertical-align: middle;">
                                        <input type="text" class="form-control stream-url-input" value="{{ $stream['url'] }}" readonly style="font-size: 11px; font-family: monospace; background: rgba(0,0,0,0.3); color: var(--text-muted); border: 1px solid rgba(255,255,255,0.03);" onclick="this.select()">
                                    </td>
                                    
                                    <!-- Distribution dropdown & buttons -->
                                    <td style="padding: 12px; vertical-align: middle; text-align: center;">
                                        <div style="display: flex; flex-direction: column; gap: 8px; align-items: stretch;">
                                            <select name="distributions[{{ $index }}]" class="form-control select-channel" style="font-size: 12px; background: rgba(15,11,24,0.9); color: #fff; border: 1px solid var(--border-glass); padding: 8px; width: 100%;">
                                                <option value="">-- تخطي (عدم التوزيع) --</option>
                                                @foreach($channels as $channel)
                                                    @php
                                                        $isSelected = false;
                                                        if ($stream['matched_channel_id'] !== null) {
                                                            $isSelected = ($channel->id == $stream['matched_channel_id']);
                                                        } else {
                                                            $normalizedGuessed = strtolower(str_replace(' ', '', $stream['guessed_name']));
                                                            $normalizedChannel = strtolower(str_replace(' ', '', $channel->name));
                                                            $isSelected = str_contains($normalizedGuessed, $normalizedChannel) || str_contains($normalizedChannel, $normalizedGuessed);
                                                        }
                                                    @endphp
                                                    <option value="{{ $channel->id }}" data-name="{{ $channel->name }}" data-logo="{{ $channel->logo_url }}" {{ $isSelected ? 'selected' : '' }}>
                                                        {{ $channel->name }} {{ $channel->name_ar ? '('.$channel->name_ar.')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            
                                            <div style="display: flex; gap: 4px; justify-content: space-between;">
                                                <!-- Button: Quick Distribute (saves as primary) -->
                                                <button type="button" class="btn btn-xs btn-primary btn-quick-distribute" 
                                                        data-url="{{ $stream['url'] }}" 
                                                        data-index="{{ $index }}" 
                                                        style="flex: 1; padding: 6px 4px; font-size: 10px; font-weight: 700; border-radius: 4px; background: linear-gradient(135deg, var(--accent-primary), #00b894); color: #06080f;"
                                                        title="توزيع كبث رئيسي للقناة المحددة">
                                                    ⚡ توزيع
                                                </button>
                                                
                                                <!-- Button: Add as Backup or Server -->
                                                <button type="button" class="btn btn-xs btn-secondary btn-options-distribute" 
                                                        data-url="{{ $stream['url'] }}" 
                                                        data-index="{{ $index }}" 
                                                        style="flex: 1; padding: 6px 4px; font-size: 10px; font-weight: 700; border-radius: 4px; background: rgba(79,126,249,0.15); border: 1px solid rgba(79,126,249,0.3); color: var(--accent-secondary);"
                                                        title="خيارات توزيع إضافية (بث احتياطي أو سيرفر)">
                                                    🔗 إضافي
                                                </button>

                                                <!-- Button: Add New Channel -->
                                                <button type="button" class="btn btn-xs btn-gold btn-new-channel-modal" 
                                                        data-url="{{ $stream['url'] }}" 
                                                        data-index="{{ $index }}" 
                                                        style="flex: 1; padding: 6px 4px; font-size: 10px; font-weight: 700; border-radius: 4px; background: linear-gradient(135deg, var(--accent-gold), #d4841a); color: #06080f;"
                                                        title="إنشاء قناة جديدة لهذا البث">
                                                    ➕ جديدة
                                                </button>
                                            </div>
                                            
                                            <!-- Status Indicator for AJAX response -->
                                            <div class="ajax-status-indicator" id="status_{{ $index }}" style="font-size: 11px; display: none; margin-top: 2px; text-align: center;"></div>
                                        </div>
                                        
                                        <!-- Hidden input to hold URL -->
                                        <input type="hidden" id="url_{{ $index }}" value="{{ $stream['url'] }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Play buttons click handler - open in popup window
    document.querySelectorAll('.btn-play-stream').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            let name = this.getAttribute('data-name');
            let logo = '';
            
            // Try to read the selected channel from the dropdown on this row
            const row = this.closest('tr');
            const select = row.querySelector('.select-channel');
            if (select && select.value) {
                const selectedOption = select.options[select.selectedIndex];
                const channelName = selectedOption.getAttribute('data-name');
                const channelLogo = selectedOption.getAttribute('data-logo');
                if (channelName) {
                    name = channelName;
                }
                if (channelLogo) {
                    logo = channelLogo;
                }
            }
            
            // Build the URL for the player view
            let playerUrl = `{{ route('admin.har.player') }}?url=${encodeURIComponent(url)}&name=${encodeURIComponent(name)}`;
            if (logo) {
                playerUrl += `&logo=${encodeURIComponent(logo)}`;
            }
            
            // Open in a popup window
            // Target the same window name 'StreamTester' to reuse it when checking multiple channels
            const width = 850;
            const height = 600;
            const left = (window.screen.width - width) / 2;
            const top = (window.screen.height - height) / 2;
            
            window.open(playerUrl, 'StreamTester', `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes,status=no,toolbar=no,menubar=no,location=no`);
        });
    });

    // Asynchronous background status check
    document.addEventListener('DOMContentLoaded', function() {
        const cells = document.querySelectorAll('.link-status-cell');
        const checkQueue = Array.from(cells);
        const maxConcurrent = 3; // Keep concurrent requests low to avoid session locking
        let activeChecks = 0;
        
        function processQueue() {
            if (checkQueue.length === 0 && activeChecks === 0) {
                return;
            }
            
            while (activeChecks < maxConcurrent && checkQueue.length > 0) {
                const cell = checkQueue.shift();
                activeChecks++;
                checkLink(cell);
            }
        }
        
        function checkLink(cell) {
            const url = cell.getAttribute('data-url');
            const badge = cell.querySelector('.status-badge');
            const row = cell.parentElement;
            const nameCell = row.querySelector('.channel-name-cell');
            const select = row.querySelector('.select-channel');
            
            // If TS file, do a HEAD request to avoid downloading the video chunk
            const isTs = url.toLowerCase().includes('.ts');
            const fetchMethod = isTs ? 'HEAD' : 'GET';
            
            // Try client-side fetch first (bypasses Cloudflare block if browser has solved challenge/cookie)
            fetch(url, { method: fetchMethod })
                .then(res => {
                    if (res.ok) {
                        badge.className = 'badge badge-success';
                        badge.style.background = 'rgba(0,212,170,0.15)';
                        badge.style.color = 'var(--success)';
                        badge.innerText = '🟢 يعمل';
                        
                        if (!isTs) {
                            return res.text();
                        }
                    } else if (res.status === 404 || res.status === 410) {
                        badge.className = 'badge badge-danger';
                        badge.style.background = 'rgba(255,90,126,0.15)';
                        badge.style.color = 'var(--danger)';
                        badge.innerText = '🔴 لا يعمل';
                        badge.title = 'الرابط غير موجود على السيرفر (404)';
                        return null; // Exit without falling back
                    } else {
                        throw new Error('HTTP Status: ' + res.status);
                    }
                })
                .then(content => {
                    if (content) {
                        let metadataName = null;
                        const nameMatch = content.match(/NAME="([^"]+)"/i);
                        if (nameMatch) {
                            metadataName = nameMatch[1];
                        } else {
                            const extinfMatch = content.match(/#EXTINF:[^,\n]*,([^\n\r]+)/i);
                            if (extinfMatch && !extinfMatch[1].startsWith('#')) {
                                metadataName = extinfMatch[1].trim();
                            }
                        }
                        
                        if (metadataName) {
                            const originalName = nameCell.innerText.trim();
                            if (!originalName.includes('[البث:')) {
                                nameCell.innerHTML = `${originalName} <br><small style="color: #00d4aa; font-weight: normal; font-size: 11px;">[البث: ${metadataName}]</small>`;
                            }
                            
                            // Auto-select in dropdown if not already selected
                            if (!select.value) {
                                const normalizedMeta = metadataName.toLowerCase().replace(/[^a-z0-9]/g, '');
                                for (let option of select.options) {
                                    if (option.value) {
                                        const normalizedOption = option.text.toLowerCase().replace(/[^a-z0-9]/g, '');
                                        if (normalizedMeta.includes(normalizedOption) || normalizedOption.includes(normalizedMeta)) {
                                            option.selected = true;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                })
                .catch(err => {
                    // Fallback to server-side check (e.g. if client-side fetch got blocked by CORS)
                    fetch(`{{ route('admin.har.check-link') }}?url=${encodeURIComponent(url)}`)
                        .then(res => res.json())
                        .then(data => {
                            // If backend successfully connected
                            if (data.status === 'active') {
                                badge.className = 'badge badge-success';
                                badge.style.background = 'rgba(0,212,170,0.15)';
                                badge.style.color = 'var(--success)';
                                badge.innerText = '🟢 يعمل';
                                
                                if (data.metadata_name) {
                                    const originalName = nameCell.innerText.trim();
                                    if (!originalName.includes('[البث:')) {
                                        nameCell.innerHTML = `${originalName} <br><small style="color: #00d4aa; font-weight: normal; font-size: 11px;">[البث: ${data.metadata_name}]</small>`;
                                    }
                                    
                                    if (!select.value) {
                                        const normalizedMeta = data.metadata_name.toLowerCase().replace(/[^a-z0-9]/g, '');
                                        for (let option of select.options) {
                                            if (option.value) {
                                                const normalizedOption = option.text.toLowerCase().replace(/[^a-z0-9]/g, '');
                                                if (normalizedMeta.includes(normalizedOption) || normalizedOption.includes(normalizedMeta)) {
                                                    option.selected = true;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            } else if (data.http_code === 403) {
                                // If blocked by Cloudflare firewall, show as Unconfirmed/Protected
                                badge.className = 'badge badge-warning';
                                badge.style.background = 'rgba(240,180,41,0.12)';
                                badge.style.color = 'var(--warning)';
                                badge.innerText = '🟡 غير مؤكد (محمي)';
                                badge.title = 'الرابط محمي بجدار حماية Cloudflare. يرجى فتح الرابط في متصفحك مرة واحدة لتخطي الحماية ثم تحديث الصفحة.';
                            } else {
                                badge.className = 'badge badge-danger';
                                badge.style.background = 'rgba(255,90,126,0.15)';
                                badge.style.color = 'var(--danger)';
                                badge.innerText = '🔴 لا يعمل';
                            }
                        })
                        .catch(serverErr => {
                            badge.className = 'badge badge-danger';
                            badge.style.background = 'rgba(255,90,126,0.15)';
                            badge.style.color = 'var(--danger)';
                            badge.innerText = '🔴 لا يعمل';
                        });
                })
                .finally(() => {
                    activeChecks--;
                    processQueue();
                });
        }
        
        processQueue();
    });

    function downloadTextFile() {
        let text = "=== روابط البث المكتشفة من ملف الشبكة ===\n\n";
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach((row, index) => {
            const name = row.querySelector('.channel-name-cell').innerText.replace(/\n/g, ' ').trim();
            const type = row.cells[0].innerText.trim();
            const status = row.querySelector('.status-badge').innerText.trim();
            const url = row.querySelector('.stream-url-input').value;
            text += `القناة: ${name} (${type}) - الحالة: ${status}\nالرابط: ${url}\n\n`;
        });

        const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'extracted_streams.txt';
        link.click();
    }

    // Form submit listener to dynamically modify parameters
    document.querySelector('form').addEventListener('submit', function(e) {
        const selects = document.querySelectorAll('.select-channel');
        selects.forEach((select, idx) => {
            const channelId = select.value;
            if (channelId) {
                const urlInput = document.getElementById('url_' + idx);
                // Create a temporary hidden input to hold the mapping
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'distributions[' + channelId + ']';
                hiddenInput.value = urlInput.value;
                this.appendChild(hiddenInput);
            }
        });
        
        // Remove standard drop downs from submitting raw values to keep request small
        selects.forEach(select => select.removeAttribute('name'));
    });
</script>

<!-- Custom Modal Styles -->
<style>
    /* Modal Overlay */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(4, 6, 12, 0.85);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }
    .modal-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }

    /* Modal Content Box */
    .modal-content {
        background: var(--bg-card);
        border: 1px solid var(--border-glass);
        border-radius: var(--radius-lg);
        width: 90%;
        max-width: 500px;
        padding: 24px;
        box-shadow: var(--shadow-premium), 0 0 30px rgba(0, 212, 170, 0.1);
        transform: scale(0.9);
        transition: transform 0.3s ease;
        direction: rtl;
        text-align: right;
    }
    .modal-overlay.active .modal-content {
        transform: scale(1);
    }

    /* Modal Header */
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--border-glass);
        padding-bottom: 12px;
        margin-bottom: 20px;
    }
    .modal-header h3 {
        font-size: 18px;
        font-weight: 800;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }
    .modal-close-btn {
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: 24px;
        cursor: pointer;
        transition: var(--transition-smooth);
        line-height: 1;
    }
    .modal-close-btn:hover {
        color: var(--danger);
    }
</style>

<!-- Modal 1: Create New Channel -->
<div class="modal-overlay" id="createChannelModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>
                <span>➕</span> إنشاء قناة جديدة للبث
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('createChannelModal')">&times;</button>
        </div>
        <form id="createChannelForm">
            <input type="hidden" name="url" id="new_channel_url">
            <input type="hidden" name="row_index" id="new_channel_row_index">
            
            <div class="form-group">
                <label for="new_channel_name">اسم القناة بالإنجليزية <span style="color: var(--danger);">*</span></label>
                <input type="text" id="new_channel_name" name="name" class="form-control" placeholder="مثال: beIN SPORTS 1 HD" required>
            </div>
            
            <div class="form-group">
                <label for="new_channel_name_ar">اسم القناة بالعربية</label>
                <input type="text" id="new_channel_name_ar" name="name_ar" class="form-control" placeholder="مثال: بين سبورت 1">
            </div>

            <div class="form-group">
                <label for="new_channel_category">التصنيف <span style="color: var(--danger);">*</span></label>
                <select id="new_channel_category" name="category_id" class="form-control" required style="background-color: rgba(6, 8, 15, 0.7); color: #fff;">
                    <option value="">-- اختر التصنيف --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }} {{ $category->name_ar ? '('.$category->name_ar.')' : '' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="new_channel_logo">رابط شعار القناة (Logo URL)</label>
                <input type="text" id="new_channel_logo" name="logo_url" class="form-control" placeholder="رابط شعار القناة (Logo URL)">
            </div>

            <div class="form-group">
                <label for="new_channel_quality">الجودة <span style="color: var(--danger);">*</span></label>
                <select id="new_channel_quality" name="quality" class="form-control" required style="background-color: rgba(6, 8, 15, 0.7); color: #fff;">
                    <option value="SD">SD</option>
                    <option value="HD" selected>HD</option>
                    <option value="FHD">FHD</option>
                    <option value="4K">4K</option>
                </select>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('createChannelModal')">إلغاء</button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSubmitNewChannel">إنشاء القناة وحفظ البث</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Distribute Options (Backup/Server) -->
<div class="modal-overlay" id="optionsDistributeModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>
                <span>🔗</span> خيارات توزيع البث الإضافية
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('optionsDistributeModal')">&times;</button>
        </div>
        <form id="optionsDistributeForm">
            <input type="hidden" name="url" id="opt_channel_url">
            <input type="hidden" name="row_index" id="opt_channel_row_index">

            <div class="form-group">
                <label for="opt_channel_select">اختر القناة <span style="color: var(--danger);">*</span></label>
                <select id="opt_channel_select" name="channel_id" class="form-control" required style="background-color: rgba(6, 8, 15, 0.7); color: #fff;">
                    <option value="">-- اختر القناة المستهدفة --</option>
                    @foreach($channels as $channel)
                        <option value="{{ $channel->id }}">{{ $channel->name }} {{ $channel->name_ar ? '('.$channel->name_ar.')' : '' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 8px;">نوع التوزيع <span style="color: var(--danger);">*</span></label>
                <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 8px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #fff; font-weight: normal; font-size: 14px;">
                        <input type="radio" name="mode" value="backup" checked onclick="toggleServerNameField(false)" style="accent-color: var(--accent-primary);">
                        <span>تعيين كبث احتياطي (Backup URL)</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #fff; font-weight: normal; font-size: 14px;">
                        <input type="radio" name="mode" value="server" onclick="toggleServerNameField(true)" style="accent-color: var(--accent-primary);">
                        <span>إضافة كسيرفر إضافي (New Server)</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #fff; font-weight: normal; font-size: 14px;">
                        <input type="radio" name="mode" value="primary" onclick="toggleServerNameField(false)" style="accent-color: var(--accent-primary);">
                        <span>تجاوز وتعيين كبث رئيسي (Primary URL)</span>
                    </label>
                </div>
            </div>

            <div class="form-group" id="serverNameField" style="display: none;">
                <label for="opt_server_name">اسم السيرفر المخصص <span style="color: var(--danger);">*</span></label>
                <input type="text" id="opt_server_name" name="server_name" class="form-control" value="Server 2" placeholder="مثال: Server 2 أو البث الاحتياطي 2">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('optionsDistributeModal')">إلغاء</button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSubmitOptions">حفظ التوزيع</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Modal controls
    function openModal(id) {
        document.getElementById(id).classList.add('active');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    // Toggle server name visibility
    function toggleServerNameField(show) {
        const field = document.getElementById('serverNameField');
        if (show) {
            field.style.display = 'block';
        } else {
            field.style.display = 'none';
        }
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Quick distribute button handler
    document.querySelectorAll('.btn-quick-distribute').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const select = row.querySelector('.select-channel');
            const channelId = select.value;
            const url = this.getAttribute('data-url');
            const index = this.getAttribute('data-index');
            const statusDiv = document.getElementById('status_' + index);

            if (!channelId) {
                alert('الرجاء اختيار قناة من القائمة المنسدلة أولاً.');
                return;
            }

            // Show loading state
            statusDiv.style.display = 'block';
            statusDiv.style.color = 'var(--text-muted)';
            statusDiv.innerText = '⏳ جاري الحفظ...';
            this.disabled = true;

            fetch('{{ route("admin.har.quick-distribute") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    channel_id: channelId,
                    url: url,
                    mode: 'primary'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    statusDiv.style.color = 'var(--success)';
                    statusDiv.innerText = '🟢 تم الحفظ بنجاح!';
                    const badgeCell = row.cells[4];
                    if (badgeCell) {
                        badgeCell.innerHTML = `<span class="badge badge-success" style="font-size: 10px; background: rgba(0,212,170,0.15); color: var(--success);" title="نفس الرابط مسجل لهذه القناة بالكامل">🟢 مطابق تماماً</span>`;
                    }
                } else {
                    statusDiv.style.color = 'var(--danger)';
                    statusDiv.innerText = '❌ خطأ: ' + (data.message || 'فشل الحفظ');
                }
            })
            .catch(err => {
                statusDiv.style.color = 'var(--danger)';
                statusDiv.innerText = '❌ خطأ في الاتصال بالسيرفر';
                console.error(err);
            })
            .finally(() => {
                this.disabled = false;
                setTimeout(() => {
                    statusDiv.style.display = 'none';
                }, 4000);
            });
        });
    });

    // Options distribute button handler
    document.querySelectorAll('.btn-options-distribute').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const url = this.getAttribute('data-url');
            const index = this.getAttribute('data-index');
            const select = row.querySelector('.select-channel');
            const channelId = select ? select.value : '';

            document.getElementById('opt_channel_url').value = url;
            document.getElementById('opt_channel_row_index').value = index;
            document.getElementById('opt_channel_select').value = channelId;
            
            // Reset modal form
            document.querySelector('input[name="mode"][value="backup"]').checked = true;
            toggleServerNameField(false);
            document.getElementById('opt_server_name').value = 'Server 2';

            openModal('optionsDistributeModal');
        });
    });

    // Handle submit for options distribute
    document.getElementById('optionsDistributeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('btnSubmitOptions');
        const originalText = submitBtn.innerText;
        submitBtn.disabled = true;
        submitBtn.innerText = '⏳ جاري الحفظ...';

        const formData = new FormData(this);
        const payload = {};
        formData.forEach((value, key) => { payload[key] = value; });

        const rowIndex = payload.row_index;
        const statusDiv = document.getElementById('status_' + rowIndex);

        fetch('{{ route("admin.har.quick-distribute") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                closeModal('optionsDistributeModal');
                
                if (statusDiv) {
                    statusDiv.style.display = 'block';
                    statusDiv.style.color = 'var(--success)';
                    statusDiv.innerText = '🟢 تم الحفظ بنجاح!';
                    setTimeout(() => { statusDiv.style.display = 'none'; }, 4000);
                }

                const activeRow = document.querySelector(`.btn-options-distribute[data-index="${rowIndex}"]`).closest('tr');
                const activeSelect = activeRow.querySelector('.select-channel');
                if (activeSelect) {
                    activeSelect.value = payload.channel_id;
                }

                const badgeCell = activeRow.cells[4];
                if (badgeCell) {
                    if (payload.mode === 'primary') {
                        badgeCell.innerHTML = `<span class="badge badge-success" style="font-size: 10px; background: rgba(0,212,170,0.15); color: var(--success);" title="نفس الرابط مسجل لهذه القناة بالكامل">🟢 مطابق تماماً</span>`;
                    } else if (payload.mode === 'backup') {
                        badgeCell.innerHTML = `<span class="badge badge-warning" style="font-size: 10px; background: rgba(240,180,41,0.15); color: var(--warning);" title="تم الحفظ كبث احتياطي لهذه القناة">🟡 بث احتياطي</span>`;
                    } else {
                        badgeCell.innerHTML = `<span class="badge badge-info" style="font-size: 10px; background: rgba(79,126,249,0.15); color: var(--accent-secondary);" title="تمت إضافة الرابط كخادم/سيرفر للبث">🔵 سيرفر إضافي</span>`;
                    }
                }
            } else {
                alert('خطأ أثناء حفظ التوزيع: ' + (data.message || 'غير معروف'));
            }
        })
        .catch(err => {
            alert('خطأ في الاتصال بالسيرفر أثناء حفظ التوزيع.');
            console.error(err);
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerText = originalText;
        });
    });

    // New Channel Modal button handler
    document.querySelectorAll('.btn-new-channel-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const url = this.getAttribute('data-url');
            const index = this.getAttribute('data-index');
            
            let guessedName = '';
            const nameCell = row.querySelector('.channel-name-cell');
            if (nameCell) {
                // Get the first line (excluding the [البث: ...] sub-text if present)
                guessedName = nameCell.innerText.split('\n')[0].trim();
            }

            document.getElementById('new_channel_url').value = url;
            document.getElementById('new_channel_row_index').value = index;
            document.getElementById('new_channel_name').value = guessedName;
            document.getElementById('new_channel_name_ar').value = '';
            document.getElementById('new_channel_logo').value = '';
            
            openModal('createChannelModal');
        });
    });

    // Handle submit for creating a new channel
    document.getElementById('createChannelForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('btnSubmitNewChannel');
        const originalText = submitBtn.innerText;
        submitBtn.disabled = true;
        submitBtn.innerText = '⏳ جاري الإنشاء...';

        const formData = new FormData(this);
        const payload = {};
        formData.forEach((value, key) => { payload[key] = value; });

        const rowIndex = payload.row_index;
        const statusDiv = document.getElementById('status_' + rowIndex);

        fetch('{{ route("admin.har.create-channel-ajax") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                closeModal('createChannelModal');
                
                if (statusDiv) {
                    statusDiv.style.display = 'block';
                    statusDiv.style.color = 'var(--success)';
                    statusDiv.innerText = '🟢 تم إنشاء القناة وحفظ البث!';
                    setTimeout(() => { statusDiv.style.display = 'none'; }, 4000);
                }

                const newChannel = data.channel;
                
                // Add option to target channel select in optionsDistributeModal
                const optSelect = document.getElementById('opt_channel_select');
                if (optSelect) {
                    const opt = document.createElement('option');
                    opt.value = newChannel.id;
                    opt.text = newChannel.name + (newChannel.name_ar ? ' (' + newChannel.name_ar + ')' : '');
                    optSelect.appendChild(opt);
                }

                // Add option to all row selects
                const selects = document.querySelectorAll('.select-channel');
                selects.forEach(select => {
                    const opt = document.createElement('option');
                    opt.value = newChannel.id;
                    opt.setAttribute('data-name', newChannel.name);
                    opt.setAttribute('data-logo', newChannel.logo_url);
                    opt.text = newChannel.name + (newChannel.name_ar ? ' (' + newChannel.name_ar + ')' : '');
                    select.appendChild(opt);
                });

                // Auto-select for the active row
                const activeRow = document.querySelector(`.btn-new-channel-modal[data-index="${rowIndex}"]`).closest('tr');
                const activeSelect = activeRow.querySelector('.select-channel');
                if (activeSelect) {
                    activeSelect.value = newChannel.id;
                }

                const badgeCell = activeRow.cells[4];
                if (badgeCell) {
                    badgeCell.innerHTML = `<span class="badge badge-success" style="font-size: 10px; background: rgba(0,212,170,0.15); color: var(--success);" title="نفس الرابط مسجل لهذه القناة بالكامل">🟢 مطابق تماماً</span>`;
                }
            } else {
                alert('خطأ أثناء إنشاء القناة: ' + (data.message || 'غير معروف'));
            }
        })
        .catch(err => {
            alert('خطأ في الاتصال بالسيرفر أثناء إنشاء القناة.');
            console.error(err);
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerText = originalText;
        });
    });
</script>
@endsection
