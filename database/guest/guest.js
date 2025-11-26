/**
 * Guest Panel JavaScript
 * Misafir görünümü için sınırlı özellikler
 */
class GuestDashboard extends DatabaseDashboard {
    constructor() {
        super();
        this.currentSection = 'overview';
        this.initGuest();
    }

    // Guest başlatma
    initGuest() {
        this.bindGuestEvents();
        this.loadPublicData();
        this.initializePublicCharts();
        this.applyTheme(this.config.theme || 'light');
        console.log('Guest Dashboard initialized');
    }

    // Guest event'leri
    bindGuestEvents() {
        // Navigasyon
        $(document).on('click', '.nav-link', (e) => {
            e.preventDefault();
            const target = $(e.currentTarget).attr('href').substring(1);
            this.switchSection(target);
        });

        // Login modal
        $(document).on('click', '#login-btn', () => this.showLoginModal());
        $(document).on('click', '#login-modal-close, #login-cancel', () => this.hideLoginModal());
        $(document).on('click', '#login-submit', () => this.handleLogin());

        // Arama
        $(document).on('input', '#public-table-search', (e) => {
            const term = e.target.value.toLowerCase();
            $('.public-table-item').each(function () {
                const name = $(this).find('.table-name-public').text().toLowerCase();
                $(this).toggle(name.includes(term));
            });
        });
    }

    // Bölüm değiştir
    switchSection(sectionId) {
        $('.guest-section').addClass('hidden');
        $(`#${sectionId}`).removeClass('hidden');
        $('.nav-item').removeClass('active');
        $(`.nav-link[href="#${sectionId}"]`).parent().addClass('active');
        this.currentSection = sectionId;
        this.loadSectionData(sectionId);
    }

    // Bölüm verileri
    loadSectionData(sectionId) {
        switch (sectionId) {
            case 'overview':
                this.loadPublicData();
                break;
            case 'public-tables':
                this.loadPublicTables();
                break;
            case 'statistics':
                this.loadPublicStats();
                break;
        }
    }

    // Genel verileri yükle
    async loadPublicData() {
        try {
            this.showLoading(true);
            const [status, stats] = await Promise.all([
                this.checkConnection(),
                this.getDatabaseStats()
            ]);
            this.updateConnectionStatus(status);
            this.updatePublicStats(stats);
            await this.loadPublicTables();
            await this.loadPublicStats();
        } catch (err) {
            console.error(err);
            this.showToast('Veriler yüklenemedi', 'error');
        } finally {
            this.showLoading(false);
        }
    }

    // Public tabloları yükle
    async loadPublicTables() {
        try {
            const tables = await this.getTables();
            const publicTables = (tables || []).filter(t => t.is_public !== false);
            this.updatePublicTablesList(publicTables);
        } catch (err) {
            console.error(err);
            this.showToast('Tablolar yüklenemedi', 'error');
        }
    }

    // Public istatistikleri yükle
    async loadPublicStats() {
        try {
            const stats = await this.getDatabaseStats();
            this.updatePublicStats(stats);
            this.updatePublicCharts(stats);
        } catch (err) {
            console.error(err);
        }
    }

    // Public tablolar listesi
    updatePublicTablesList(tables) {
        const container = $('#public-tables-list');
        container.empty();

        if (!tables || tables.length === 0) {
            container.html('<div class="no-data">Genel tablo bulunamadı</div>');
            return;
        }

        tables.forEach(table => {
            const item = $(`
                <div class="public-table-item">
                    <div class="table-header-public">
                        <h4 class="table-name-public">${table.name}</h4>
                        <span class="table-count-public">${table.records} kayıt</span>
                    </div>
                    <div class="table-info-public">
                        <div class="info-item-public">
                            <i class="fas fa-database"></i>
                            <span>Boyut: ${this.formatBytes(table.size)}</span>
                        </div>
                        <div class="info-item-public">
                            <i class="fas fa-clock"></i>
                            <span>Son güncelleme: ${this.formatDate(table.updated_at)}</span>
                        </div>
                    </div>
                </div>
            `);
            container.append(item);
        });
    }

    // Public istatistikleri güncelle
    updatePublicStats(stats) {
        $('#public-tables-count').text(stats.tables || 0);
        $('#public-db-size').text(this.formatBytes(stats.size || 0));
        $('#public-uptime').text((stats.uptime || 0) + ' sn');
        $('#public-status').text((stats.status || 'Aktif'));

        $('#summary-tables').text(stats.tables || 0);
        $('#summary-records').text(this.formatNumber(stats.records || 0));
        const avgSize = (stats.tables || 0) > 0 ? (stats.size || 0) / stats.tables : 0;
        $('#summary-avg-size').text(this.formatBytes(avgSize));
        $('#summary-largest').text((stats.largest_table && stats.largest_table.name) || '--');

        if (stats.platform) $('#platform').text(stats.platform);
        if (stats.db_version) $('#db-version').text(stats.db_version);
        if (stats.total_connections !== undefined) $('#total-connections').text(stats.total_connections);
        if (stats.last_update) $('#last-update').text(this.formatDate(stats.last_update));
    }

    // Login modal
    showLoginModal() {
        $('#login-modal').removeClass('hidden');
    }
    hideLoginModal() {
        $('#login-modal').addClass('hidden');
    }

    // Basit login (demo)
    async handleLogin() {
        const username = $('#username').val().trim();
        const password = $('#password').val().trim();
        if (!username || !password) {
            this.showToast('Kullanıcı adı ve şifre gereklidir', 'warning');
            return;
        }
        try {
            this.showToast('Giriş yapılıyor...', 'info');
            // Backend entegrasyonu burada yapılabilir
            setTimeout(() => {
                this.showToast('Giriş başarılı, yönlendiriliyorsunuz...', 'success');
                this.hideLoginModal();
                window.location.href = '../user/index.html';
            }, 800);
        } catch (err) {
            this.showToast('Giriş başarısız', 'error');
        }
    }

    // Public chart başlatma
    initializePublicCharts() {
        const tableCtx = document.getElementById('public-table-chart');
        if (tableCtx) {
            window.publicTableChart = new Chart(tableCtx, {
                type: 'doughnut',
                data: { labels: [], datasets: [{ data: [], backgroundColor: ['#60a5fa', '#a78bfa', '#f472b6', '#34d399', '#fbbf24'] }] },
                options: { responsive: true }
            });
        }
        const usageCtx = document.getElementById('public-usage-chart');
        if (usageCtx) {
            window.publicUsageChart = new Chart(usageCtx, {
                type: 'line',
                data: { labels: [], datasets: [{ label: 'Kullanım', data: [], borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.1)', tension: 0.2 }] },
                options: { responsive: true, scales: { y: { beginAtZero: true, max: 100 } } }
            });
        }
    }

    // Public chart güncelle
    updatePublicCharts(stats) {
        if (window.publicTableChart && stats.table_sizes) {
            window.publicTableChart.data.labels = stats.table_sizes.map(t => t.name);
            window.publicTableChart.data.datasets[0].data = stats.table_sizes.map(t => t.size);
            window.publicTableChart.update();
        }
        if (window.publicUsageChart && stats.usage_history) {
            window.publicUsageChart.data.labels = stats.usage_history.map(p => this.formatTimeLabel(p.timestamp));
            window.publicUsageChart.data.datasets[0].data = stats.usage_history.map(p => p.usage);
            window.publicUsageChart.update();
        }
    }

    formatTimeLabel(ts) {
        const d = new Date(ts);
        return d.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' });
    }
}

// Başlat
const guestDashboard = new GuestDashboard();
window.guestDashboard = guestDashboard;


