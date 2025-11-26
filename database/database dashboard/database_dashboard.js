/**
 * Veritabanı Gösterge Paneli
 * Modern, responsive ve interaktif veritabanı yönetim arayüzü
 */
class DatabaseDashboard {
    constructor() {
        this.currentDatabase = null;
        this.refreshInterval = null;
        this.isConnected = false;
        this.stats = {
            tables: 0,
            records: 0,
            size: 0,
            connections: 0
        };
        this.init();
    }

    /**
     * Dashboard'u başlat
     */
    init() {
        this.loadConfiguration();
        this.bindEvents();
        this.loadData();
        this.startAutoRefresh();
        this.initializeCharts();
        console.log('Database Dashboard initialized');
    }

    /**
     * Konfigürasyonu yükle
     */
    loadConfiguration() {
        const config = localStorage.getItem('db_dashboard_config') || '{}';
        this.config = JSON.parse(config);

        // Varsayılan ayarlar
        this.config = {
            autoRefresh: true,
            refreshInterval: 30000, // 30 saniye
            theme: 'light',
            showAdvanced: false,
            ...this.config
        };
    }

    /**
     * Event listener'ları bağla
     */
    bindEvents() {
        // Yenileme butonu
        $(document).on('click', '.refresh-btn', () => {
            this.loadData();
            this.showToast('Veriler yenilendi', 'success');
        });

        // Bağlantı testi
        $(document).on('click', '.test-connection-btn', () => {
            this.testConnection();
        });

        // Tablo seçimi
        $(document).on('click', '.table-item', (e) => {
            const tableName = $(e.currentTarget).data('table');
            this.showTableDetails(tableName);
        });

        // Ayarlar modalı
        $(document).on('click', '.settings-btn', () => {
            this.showSettings();
        });

        // Tema değiştirme
        $(document).on('click', '.theme-toggle', () => {
            this.toggleTheme();
        });

        // Klavye kısayolları
        $(document).on('keydown', (e) => {
            if (e.ctrlKey || e.metaKey) {
                switch (e.key) {
                    case 'r':
                        e.preventDefault();
                        this.loadData();
                        break;
                    case 's':
                        e.preventDefault();
                        this.showSettings();
                        break;
                }
            }
        });
    }

    /**
     * Veritabanı verilerini yükle
     */
    async loadData() {
        try {
            this.showLoading(true);

            // Paralel API çağrıları
            const [connectionStatus, tables, stats, performance] = await Promise.all([
                this.checkConnection(),
                this.getTables(),
                this.getDatabaseStats(),
                this.getPerformanceMetrics()
            ]);

            this.isConnected = connectionStatus.connected;
            this.updateConnectionStatus(connectionStatus);
            this.updateTablesList(tables);
            this.updateStats(stats);
            this.updatePerformanceMetrics(performance);
            this.updateCharts();

        } catch (error) {
            console.error('Veri yükleme hatası:', error);
            this.showToast('Veri yükleme hatası: ' + error.message, 'error');
        } finally {
            this.showLoading(false);
        }
    }

    /**
     * Bağlantı durumunu kontrol et
     */
    async checkConnection() {
        try {
            const response = await fetch('/api/database/status');
            const data = await response.json();
            return data;
        } catch (error) {
            return { connected: false, error: error.message };
        }
    }

    /**
     * Tabloları getir
     */
    async getTables() {
        try {
            const response = await fetch('/api/database/tables');
            return await response.json();
        } catch (error) {
            console.error('Tablolar yüklenemedi:', error);
            return [];
        }
    }

    /**
     * Veritabanı istatistiklerini getir
     */
    async getDatabaseStats() {
        try {
            const response = await fetch('/api/database/stats');
            return await response.json();
        } catch (error) {
            console.error('İstatistikler yüklenemedi:', error);
            return this.stats;
        }
    }

    /**
     * Performans metriklerini getir
     */
    async getPerformanceMetrics() {
        try {
            const response = await fetch('/api/database/performance');
            return await response.json();
        } catch (error) {
            console.error('Performans metrikleri yüklenemedi:', error);
            return {};
        }
    }

    /**
     * Bağlantı durumunu güncelle
     */
    updateConnectionStatus(status) {
        const statusElement = $('#connection-status');
        const statusText = $('#connection-text');

        if (status.connected) {
            statusElement.removeClass('disconnected').addClass('connected');
            statusText.text('Bağlı');
        } else {
            statusElement.removeClass('connected').addClass('disconnected');
            statusText.text('Bağlantı Yok');
        }
    }

    /**
     * Tablolar listesini güncelle
     */
    updateTablesList(tables) {
        const container = $('#tables-list');
        container.empty();

        if (tables.length === 0) {
            container.html('<div class="no-data">Tablo bulunamadı</div>');
            return;
        }

        tables.forEach(table => {
            const tableItem = $(`
                <div class="table-item" data-table="${table.name}">
                    <div class="table-header">
                        <h4>${table.name}</h4>
                        <span class="table-count">${table.records} kayıt</span>
                    </div>
                    <div class="table-info">
                        <div class="info-item">
                            <i class="fas fa-database"></i>
                            <span>Boyut: ${this.formatBytes(table.size)}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>Son güncelleme: ${this.formatDate(table.updated_at)}</span>
                        </div>
                    </div>
                    <div class="table-actions">
                        <button class="btn btn-sm btn-primary" onclick="databaseDashboard.showTableDetails('${table.name}')">
                            <i class="fas fa-eye"></i> Görüntüle
                        </button>
                        <button class="btn btn-sm btn-secondary" onclick="databaseDashboard.exportTable('${table.name}')">
                            <i class="fas fa-download"></i> Dışa Aktar
                        </button>
                    </div>
                </div>
            `);
            container.append(tableItem);
        });
    }

    /**
     * İstatistikleri güncelle
     */
    updateStats(stats) {
        $('#total-tables').text(stats.tables || 0);
        $('#total-records').text(this.formatNumber(stats.records || 0));
        $('#database-size').text(this.formatBytes(stats.size || 0));
        $('#active-connections').text(stats.connections || 0);

        this.stats = stats;
    }

    /**
     * Performans metriklerini güncelle
     */
    updatePerformanceMetrics(metrics) {
        // CPU kullanımı
        $('#cpu-usage').text(metrics.cpu_usage + '%');
        $('#cpu-usage-bar').css('width', metrics.cpu_usage + '%');

        // Bellek kullanımı
        $('#memory-usage').text(metrics.memory_usage + '%');
        $('#memory-usage-bar').css('width', metrics.memory_usage + '%');

        // Disk kullanımı
        $('#disk-usage').text(metrics.disk_usage + '%');
        $('#disk-usage-bar').css('width', metrics.disk_usage + '%');

        // Sorgu süresi
        $('#query-time').text(metrics.avg_query_time + 'ms');
    }

    /**
     * Grafikleri güncelle
     */
    updateCharts() {
        // Performans grafiği
        this.updatePerformanceChart();

        // Tablo boyutları grafiği
        this.updateTableSizeChart();

        // Bağlantı grafiği
        this.updateConnectionChart();
    }

    /**
     * Performans grafiğini güncelle
     */
    updatePerformanceChart() {
        // Chart.js ile performans grafiği
        if (window.performanceChart) {
            window.performanceChart.update();
        }
    }

    /**
     * Tablo boyutları grafiğini güncelle
     */
    updateTableSizeChart() {
        // Chart.js ile tablo boyutları grafiği
        if (window.tableSizeChart) {
            window.tableSizeChart.update();
        }
    }

    /**
     * Bağlantı grafiğini güncelle
     */
    updateConnectionChart() {
        // Chart.js ile bağlantı grafiği
        if (window.connectionChart) {
            window.connectionChart.update();
        }
    }

    /**
     * Tablo detaylarını göster
     */
    showTableDetails(tableName) {
        this.showModal('Tablo Detayları', `
            <div class="table-details">
                <h4>${tableName}</h4>
                <div class="table-structure">
                    <!-- Tablo yapısı burada gösterilecek -->
                </div>
            </div>
        `);
    }

    /**
     * Tabloyu dışa aktar
     */
    async exportTable(tableName) {
        try {
            this.showToast('Dışa aktarma başlatılıyor...', 'info');

            const response = await fetch(`/api/database/export/${tableName}`);
            const blob = await response.blob();

            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${tableName}_${new Date().toISOString().split('T')[0]}.sql`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            this.showToast('Tablo başarıyla dışa aktarıldı', 'success');
        } catch (error) {
            this.showToast('Dışa aktarma hatası: ' + error.message, 'error');
        }
    }

    /**
     * Bağlantı testi yap
     */
    async testConnection() {
        try {
            this.showToast('Bağlantı test ediliyor...', 'info');

            const response = await fetch('/api/database/test');
            const result = await response.json();

            if (result.success) {
                this.showToast('Bağlantı başarılı!', 'success');
            } else {
                this.showToast('Bağlantı başarısız: ' + result.error, 'error');
            }
        } catch (error) {
            this.showToast('Bağlantı testi hatası: ' + error.message, 'error');
        }
    }

    /**
     * Ayarları göster
     */
    showSettings() {
        this.showModal('Ayarlar', `
            <div class="settings-form">
                <div class="form-group">
                    <label>Otomatik Yenileme</label>
                    <input type="checkbox" id="auto-refresh" ${this.config.autoRefresh ? 'checked' : ''}>
                </div>
                <div class="form-group">
                    <label>Yenileme Aralığı (saniye)</label>
                    <input type="number" id="refresh-interval" value="${this.config.refreshInterval / 1000}" min="10" max="300">
                </div>
                <div class="form-group">
                    <label>Tema</label>
                    <select id="theme-select">
                        <option value="light" ${this.config.theme === 'light' ? 'selected' : ''}>Açık</option>
                        <option value="dark" ${this.config.theme === 'dark' ? 'selected' : ''}>Koyu</option>
                    </select>
                </div>
            </div>
        `, [
            { text: 'Kaydet', class: 'btn-primary', action: () => this.saveSettings() },
            { text: 'İptal', class: 'btn-secondary', action: () => this.hideModal() }
        ]);
    }

    /**
     * Ayarları kaydet
     */
    saveSettings() {
        this.config.autoRefresh = $('#auto-refresh').is(':checked');
        this.config.refreshInterval = $('#refresh-interval').val() * 1000;
        this.config.theme = $('#theme-select').val();

        localStorage.setItem('db_dashboard_config', JSON.stringify(this.config));

        if (this.config.autoRefresh) {
            this.startAutoRefresh();
        } else {
            this.stopAutoRefresh();
        }

        this.applyTheme(this.config.theme);
        this.hideModal();
        this.showToast('Ayarlar kaydedildi', 'success');
    }

    /**
     * Tema değiştir
     */
    toggleTheme() {
        const newTheme = this.config.theme === 'light' ? 'dark' : 'light';
        this.config.theme = newTheme;
        this.applyTheme(newTheme);
        localStorage.setItem('db_dashboard_config', JSON.stringify(this.config));
    }

    /**
     * Temayı uygula
     */
    applyTheme(theme) {
        document.body.className = theme === 'dark' ? 'dark-theme' : 'light-theme';
    }

    /**
     * Modal göster
     */
    showModal(title, content, buttons = []) {
        const modal = $(`
            <div class="modal-overlay">
                <div class="modal-container">
                    <div class="modal-header">
                        <h3>${title}</h3>
                        <button class="modal-close">&times;</button>
                    </div>
                    <div class="modal-body">${content}</div>
                    <div class="modal-footer">
                        ${buttons.map(btn => `<button class="btn ${btn.class}">${btn.text}</button>`).join('')}
                    </div>
                </div>
            </div>
        `);

        $('body').append(modal);
        modal.find('.modal-close, .btn-secondary').on('click', () => modal.remove());

        buttons.forEach(btn => {
            if (btn.action) {
                modal.find(`.btn:contains("${btn.text}")`).on('click', btn.action);
            }
        });
    }

    /**
     * Modal gizle
     */
    hideModal() {
        $('.modal-overlay').remove();
    }

    /**
     * Toast bildirimi göster
     */
    showToast(message, type = 'info') {
        const toast = $(`
            <div class="toast toast-${type}">
                <i class="fas fa-${this.getToastIcon(type)}"></i>
                <span>${message}</span>
                <button class="toast-close">&times;</button>
            </div>
        `);

        $('#toast-container').append(toast);

        setTimeout(() => toast.addClass('show'), 100);
        setTimeout(() => {
            toast.removeClass('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);

        toast.find('.toast-close').on('click', () => {
            toast.removeClass('show');
            setTimeout(() => toast.remove(), 300);
        });
    }

    /**
     * Toast ikonu al
     */
    getToastIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    /**
     * Loading durumunu göster/gizle
     */
    showLoading(show) {
        if (show) {
            $('#loading-overlay').addClass('show');
        } else {
            $('#loading-overlay').removeClass('show');
        }
    }

    /**
     * Otomatik yenilemeyi başlat
     */
    startAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }

        if (this.config.autoRefresh) {
            this.refreshInterval = setInterval(() => {
                this.loadData();
            }, this.config.refreshInterval);
        }
    }

    /**
     * Otomatik yenilemeyi durdur
     */
    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
    }

    /**
     * Grafikleri başlat
     */
    initializeCharts() {
        // Chart.js ile grafikleri başlat
        this.initializePerformanceChart();
        this.initializeTableSizeChart();
        this.initializeConnectionChart();
    }

    /**
     * Performans grafiğini başlat
     */
    initializePerformanceChart() {
        const ctx = document.getElementById('performance-chart');
        if (ctx) {
            window.performanceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'CPU Kullanımı',
                        data: [],
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }
    }

    /**
     * Tablo boyutları grafiğini başlat
     */
    initializeTableSizeChart() {
        const ctx = document.getElementById('table-size-chart');
        if (ctx) {
            window.tableSizeChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0',
                            '#9966FF'
                        ]
                    }]
                },
                options: {
                    responsive: true
                }
            });
        }
    }

    /**
     * Bağlantı grafiğini başlat
     */
    initializeConnectionChart() {
        const ctx = document.getElementById('connection-chart');
        if (ctx) {
            window.connectionChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Aktif', 'Bekleyen', 'Toplam'],
                    datasets: [{
                        label: 'Bağlantılar',
                        data: [0, 0, 0],
                        backgroundColor: ['#4CAF50', '#FF9800', '#2196F3']
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
        });
    }
}

    /**
     * Yardımcı fonksiyonlar
     */
    formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('tr-TR');
    }
}

// Dashboard'u başlat
const databaseDashboard = new DatabaseDashboard();

// Global erişim için
window.databaseDashboard = databaseDashboard;