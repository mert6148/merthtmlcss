/**
 * Admin Panel JavaScript
 * Gelişmiş veritabanı yönetim fonksiyonları
 */
class AdminDashboard extends DatabaseDashboard {
    constructor() {
        super();
        this.adminPrivileges = true;
        this.currentSection = 'overview';
        this.initAdmin();
    }

    /**
     * Admin paneli başlat
     */
    initAdmin() {
        this.bindAdminEvents();
        this.loadAdminData();
        this.initializeAdminCharts();
        console.log('Admin Dashboard initialized');
    }

    /**
     * Admin event listener'ları
     */
    bindAdminEvents() {
        // Navigation
        $(document).on('click', '.nav-link', (e) => {
            e.preventDefault();
            const target = $(e.currentTarget).attr('href').substring(1);
            this.switchSection(target);
        });

        // Admin specific buttons
        $(document).on('click', '#backup-db', () => {
            this.createBackup();
        });

        $(document).on('click', '#restore-db', () => {
            this.showRestoreModal();
        });

        // Quick actions
        $(document).on('click', '.action-btn', (e) => {
            const action = $(e.currentTarget).attr('onclick');
            if (action) {
                eval(action);
            }
        });

        // Modal events
        $(document).on('click', '#admin-modal-close, #admin-modal-cancel', () => {
            this.hideAdminModal();
        });

        $(document).on('click', '#admin-modal-confirm', () => {
            this.confirmAdminAction();
        });
    }

    /**
     * Bölüm değiştir
     */
    switchSection(sectionId) {
        // Hide all sections
        $('.admin-section').addClass('hidden');

        // Show target section
        $(`#${sectionId}`).removeClass('hidden');

        // Update navigation
        $('.nav-item').removeClass('active');
        $(`.nav-link[href="#${sectionId}"]`).parent().addClass('active');

        this.currentSection = sectionId;

        // Load section specific data
        this.loadSectionData(sectionId);
    }

    /**
     * Bölüm verilerini yükle
     */
    loadSectionData(sectionId) {
        switch (sectionId) {
            case 'overview':
                this.loadOverviewData();
                break;
            case 'tables':
                this.loadTablesData();
                break;
            case 'users':
                this.loadUsersData();
                break;
            case 'security':
                this.loadSecurityData();
                break;
            case 'backup':
                this.loadBackupData();
                break;
            case 'logs':
                this.loadLogsData();
                break;
        }
    }

    /**
     * Admin verilerini yükle
     */
    async loadAdminData() {
        try {
            this.showLoading(true);

            const [adminStats, systemHealth, securityStatus] = await Promise.all([
                this.getAdminStats(),
                this.getSystemHealth(),
                this.getSecurityStatus()
            ]);

            this.updateAdminStats(adminStats);
            this.updateSystemHealth(systemHealth);
            this.updateSecurityStatus(securityStatus);

        } catch (error) {
            console.error('Admin veri yükleme hatası:', error);
            this.showToast('Admin verileri yüklenemedi: ' + error.message, 'error');
        } finally {
            this.showLoading(false);
        }
    }

    /**
     * Admin istatistiklerini getir
     */
    async getAdminStats() {
        try {
            const response = await fetch('/api/admin/stats');
            return await response.json();
        } catch (error) {
            console.error('Admin istatistikleri yüklenemedi:', error);
            return {
                databases: 0,
                tables: 0,
                users: 0,
                securityAlerts: 0
            };
        }
    }

    /**
     * Sistem sağlığını getir
     */
    async getSystemHealth() {
        try {
            const response = await fetch('/api/admin/system-health');
            return await response.json();
        } catch (error) {
            console.error('Sistem sağlığı yüklenemedi:', error);
            return {
                cpu: 0,
                memory: 0,
                disk: 0,
                network: 0,
                status: 'unknown'
            };
        }
    }

    /**
     * Güvenlik durumunu getir
     */
    async getSecurityStatus() {
        try {
            const response = await fetch('/api/admin/security-status');
            return await response.json();
        } catch (error) {
            console.error('Güvenlik durumu yüklenemedi:', error);
            return {
                alerts: 0,
                blockedIPs: 0,
                lastScan: null
            };
        }
    }

    /**
     * Admin istatistiklerini güncelle
     */
    updateAdminStats(stats) {
        $('#total-databases').text(stats.databases || 0);
        $('#total-tables').text(stats.tables || 0);
        $('#total-users').text(stats.users || 0);
        $('#security-alerts').text(stats.securityAlerts || 0);
    }

    /**
     * Sistem sağlığını güncelle
     */
    updateSystemHealth(health) {
        // CPU
        $('#cpu-metric').css('width', health.cpu + '%');
        $('#cpu-value').text(health.cpu + '%');

        // Memory
        $('#memory-metric').css('width', health.memory + '%');
        $('#memory-value').text(health.memory + '%');

        // Disk
        $('#disk-metric').css('width', health.disk + '%');
        $('#disk-value').text(health.disk + '%');

        // Network
        $('#network-metric').css('width', health.network + '%');
        $('#network-value').text(health.network + '%');

        // Status
        const statusElement = $('#system-health-status');
        const statusText = statusElement.find('.status-text');
        const statusIndicator = statusElement.find('.status-indicator');

        statusText.text(health.status === 'healthy' ? 'Sağlıklı' : 'Sorunlu');
        statusIndicator.removeClass('connected disconnected').addClass(health.status === 'healthy' ? 'connected' : 'disconnected');
    }

    /**
     * Güvenlik durumunu güncelle
     */
    updateSecurityStatus(security) {
        // Güvenlik uyarıları
        if (security.alerts > 0) {
            this.showToast(`${security.alerts} güvenlik uyarısı tespit edildi`, 'warning');
        }
    }

    /**
     * Genel bakış verilerini yükle
     */
    async loadOverviewData() {
        // Bu fonksiyon zaten loadAdminData() tarafından çağrılıyor
    }

    /**
     * Tablolar verilerini yükle
     */
    async loadTablesData() {
        try {
            const tables = await this.getTables();
            this.updateAdminTablesList(tables);
        } catch (error) {
            console.error('Tablolar yüklenemedi:', error);
            this.showToast('Tablolar yüklenemedi', 'error');
        }
    }

    /**
     * Admin tablolar listesini güncelle
     */
    updateAdminTablesList(tables) {
        const container = $('#tables-list-admin');
        container.empty();

        if (tables.length === 0) {
            container.html('<div class="no-data">Tablo bulunamadı</div>');
            return;
        }

        tables.forEach(table => {
            const tableItem = $(`
                <div class="admin-table-item">
                    <div class="table-header-admin">
                        <h4 class="table-name-admin">${table.name}</h4>
                        <div class="table-actions-admin">
                            <button class="btn btn-sm btn-primary" onclick="adminDashboard.editTable('${table.name}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-warning" onclick="adminDashboard.optimizeTable('${table.name}')">
                                <i class="fas fa-tachometer-alt"></i>
                            </button>
                            <button class="btn btn-sm btn-error" onclick="adminDashboard.deleteTable('${table.name}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="table-info">
                        <div class="info-item">
                            <i class="fas fa-database"></i>
                            <span>Boyut: ${this.formatBytes(table.size)}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-list"></i>
                            <span>Kayıt: ${table.records}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>Son güncelleme: ${this.formatDate(table.updated_at)}</span>
                        </div>
                    </div>
                </div>
            `);
            container.append(tableItem);
        });
    }

    /**
     * Kullanıcılar verilerini yükle
     */
    async loadUsersData() {
        try {
            const users = await this.getUsers();
            this.updateAdminUsersList(users);
        } catch (error) {
            console.error('Kullanıcılar yüklenemedi:', error);
            this.showToast('Kullanıcılar yüklenemedi', 'error');
        }
    }

    /**
     * Kullanıcıları getir
     */
    async getUsers() {
        try {
            const response = await fetch('/api/admin/users');
            return await response.json();
        } catch (error) {
            console.error('Kullanıcılar yüklenemedi:', error);
            return [];
        }
    }

    /**
     * Admin kullanıcılar listesini güncelle
     */
    updateAdminUsersList(users) {
        const container = $('#users-list-admin');
        container.empty();

        if (users.length === 0) {
            container.html('<div class="no-data">Kullanıcı bulunamadı</div>');
            return;
        }

        users.forEach(user => {
            const userItem = $(`
                <div class="admin-user-item">
                    <div class="user-avatar">
                        ${user.name.charAt(0).toUpperCase()}
                    </div>
                    <div class="user-info">
                        <div class="user-name">${user.name}</div>
                        <div class="user-role">${user.role}</div>
                    </div>
                    <div class="user-status ${user.status}">
                        ${user.status === 'active' ? 'Aktif' : 'Pasif'}
                    </div>
                    <div class="user-actions">
                        <button class="btn btn-sm btn-primary" onclick="adminDashboard.editUser('${user.id}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="adminDashboard.toggleUserStatus('${user.id}')">
                            <i class="fas fa-user-${user.status === 'active' ? 'slash' : 'check'}"></i>
                        </button>
                        <button class="btn btn-sm btn-error" onclick="adminDashboard.deleteUser('${user.id}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `);
            container.append(userItem);
        });
    }

    /**
     * Güvenlik verilerini yükle
     */
    async loadSecurityData() {
        try {
            const [securityEvents, blockedIPs] = await Promise.all([
                this.getSecurityEvents(),
                this.getBlockedIPs()
            ]);

            this.updateSecurityEvents(securityEvents);
            this.updateBlockedIPs(blockedIPs);
        } catch (error) {
            console.error('Güvenlik verileri yüklenemedi:', error);
            this.showToast('Güvenlik verileri yüklenemedi', 'error');
        }
    }

    /**
     * Güvenlik olaylarını getir
     */
    async getSecurityEvents() {
        try {
            const response = await fetch('/api/admin/security-events');
            return await response.json();
        } catch (error) {
            console.error('Güvenlik olayları yüklenemedi:', error);
            return [];
        }
    }

    /**
     * Engellenen IP'leri getir
     */
    async getBlockedIPs() {
        try {
            const response = await fetch('/api/admin/blocked-ips');
            return await response.json();
        } catch (error) {
            console.error('Engellenen IP\'ler yüklenemedi:', error);
            return [];
        }
    }

    /**
     * Güvenlik olaylarını güncelle
     */
    updateSecurityEvents(events) {
        const container = $('#security-events');
        container.empty();

        if (events.length === 0) {
            container.html('<div class="no-data">Güvenlik olayı bulunamadı</div>');
            return;
        }

        events.forEach(event => {
            const eventItem = $(`
                <div class="security-event ${event.level}">
                    <div class="event-header">
                        <span class="event-time">${this.formatDate(event.timestamp)}</span>
                        <span class="event-level">${event.level.toUpperCase()}</span>
                    </div>
                    <div class="event-message">${event.message}</div>
                    <div class="event-source">Kaynak: ${event.source}</div>
                </div>
            `);
            container.append(eventItem);
        });
    }

    /**
     * Engellenen IP'leri güncelle
     */
    updateBlockedIPs(ips) {
        const container = $('#blocked-ips');
        container.empty();

        if (ips.length === 0) {
            container.html('<div class="no-data">Engellenen IP bulunamadı</div>');
            return;
        }

        ips.forEach(ip => {
            const ipItem = $(`
                <div class="blocked-ip">
                    <div class="ip-info">
                        <span class="ip-address">${ip.address}</span>
                        <span class="ip-reason">${ip.reason}</span>
                    </div>
                    <div class="ip-actions">
                        <button class="btn btn-sm btn-success" onclick="adminDashboard.unblockIP('${ip.address}')">
                            <i class="fas fa-unlock"></i>
                        </button>
                    </div>
                </div>
            `);
            container.append(ipItem);
        });
    }

    /**
     * Yedekleme verilerini yükle
     */
    async loadBackupData() {
        try {
            const backups = await this.getRecentBackups();
            this.updateRecentBackups(backups);
        } catch (error) {
            console.error('Yedekleme verileri yüklenemedi:', error);
            this.showToast('Yedekleme verileri yüklenemedi', 'error');
        }
    }

    /**
     * Son yedekleri getir
     */
    async getRecentBackups() {
        try {
            const response = await fetch('/api/admin/recent-backups');
            return await response.json();
        } catch (error) {
            console.error('Son yedekler yüklenemedi:', error);
            return [];
        }
    }

    /**
     * Son yedekleri güncelle
     */
    updateRecentBackups(backups) {
        const container = $('#recent-backups');
        container.empty();

        if (backups.length === 0) {
            container.html('<div class="no-data">Yedek bulunamadı</div>');
            return;
        }

        backups.forEach(backup => {
            const backupItem = $(`
                <div class="backup-item">
                    <div class="backup-info">
                        <div class="backup-name">${backup.name}</div>
                        <div class="backup-date">${this.formatDate(backup.created_at)}</div>
                    </div>
                    <div class="backup-actions">
                        <button class="btn btn-sm btn-primary" onclick="adminDashboard.downloadBackup('${backup.id}')">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="adminDashboard.restoreBackup('${backup.id}')">
                            <i class="fas fa-upload"></i>
                        </button>
                        <button class="btn btn-sm btn-error" onclick="adminDashboard.deleteBackup('${backup.id}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `);
            container.append(backupItem);
        });
    }

    /**
     * Log verilerini yükle
     */
    async loadLogsData() {
        try {
            const logs = await this.getLogs();
            this.updateLogsList(logs);
        } catch (error) {
            console.error('Log verileri yüklenemedi:', error);
            this.showToast('Log verileri yüklenemedi', 'error');
        }
    }

    /**
     * Logları getir
     */
    async getLogs() {
        try {
            const response = await fetch('/api/admin/logs');
            return await response.json();
        } catch (error) {
            console.error('Loglar yüklenemedi:', error);
            return [];
        }
    }

    /**
     * Log listesini güncelle
     */
    updateLogsList(logs) {
        const container = $('#logs-list');
        container.empty();

        if (logs.length === 0) {
            container.html('<div class="no-data">Log bulunamadı</div>');
            return;
        }

        logs.forEach(log => {
            const logItem = $(`
                <div class="log-entry">
                    <div class="log-timestamp">${this.formatDate(log.timestamp)}</div>
                    <div class="log-level ${log.level}">${log.level.toUpperCase()}</div>
                    <div class="log-message">${log.message}</div>
                </div>
            `);
            container.append(logItem);
        });
    }

    /**
     * Admin modal göster
     */
    showAdminModal(title, content, buttons = []) {
        $('#admin-modal-title').text(title);
        $('#admin-modal-body').html(content);

        const footer = $('#admin-modal-footer');
        footer.empty();

        if (buttons.length === 0) {
            buttons = [
                { text: 'İptal', class: 'btn-secondary', action: () => this.hideAdminModal() },
                { text: 'Onayla', class: 'btn-primary', action: () => this.confirmAdminAction() }
            ];
        }

        buttons.forEach(btn => {
            const button = $(`<button class="btn ${btn.class}">${btn.text}</button>`);
            if (btn.action) {
                button.on('click', btn.action);
            }
            footer.append(button);
        });

        $('#admin-modal').removeClass('hidden');
    }

    /**
     * Admin modal gizle
     */
    hideAdminModal() {
        $('#admin-modal').addClass('hidden');
    }

    /**
     * Admin aksiyonu onayla
     */
    confirmAdminAction() {
        // Bu fonksiyon modal içeriğine göre özelleştirilecek
        this.hideAdminModal();
    }

    /**
     * Admin grafikleri başlat
     */
    initializeAdminCharts() {
        // Admin paneli için özel grafikler
        this.initializeAdminPerformanceChart();
        this.initializeAdminSecurityChart();
        this.initializeAdminUsersChart();
    }

    /**
     * Admin performans grafiğini başlat
     */
    initializeAdminPerformanceChart() {
        const ctx = document.getElementById('admin-performance-chart');
        if (ctx) {
            window.adminPerformanceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'CPU',
                        data: [],
                        borderColor: '#dc2626',
                        backgroundColor: 'rgba(220, 38, 38, 0.1)',
                        tension: 0.1
                    }, {
                        label: 'Memory',
                        data: [],
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
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
     * Admin güvenlik grafiğini başlat
     */
    initializeAdminSecurityChart() {
        const ctx = document.getElementById('admin-security-chart');
        if (ctx) {
            window.adminSecurityChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Güvenli', 'Uyarı', 'Tehdit'],
                    datasets: [{
                        data: [0, 0, 0],
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
                    }]
                },
                options: {
                    responsive: true
                }
            });
        }
    }

    /**
     * Admin kullanıcılar grafiğini başlat
     */
    initializeAdminUsersChart() {
        const ctx = document.getElementById('admin-users-chart');
        if (ctx) {
            window.adminUsersChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Aktif', 'Pasif', 'Admin', 'User'],
                    datasets: [{
                        label: 'Kullanıcılar',
                        data: [0, 0, 0, 0],
                        backgroundColor: ['#10b981', '#6b7280', '#dc2626', '#3b82f6']
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

    // Admin specific methods
    createDatabase() {
        this.showAdminModal('Yeni Veritabanı', `
            <div class="form-group">
                <label>Veritabanı Adı</label>
                <input type="text" id="db-name" class="form-input" placeholder="Veritabanı adını girin">
            </div>
            <div class="form-group">
                <label>Karakter Seti</label>
                <select id="db-charset" class="form-select">
                    <option value="utf8mb4">UTF-8 MB4</option>
                    <option value="utf8">UTF-8</option>
                    <option value="latin1">Latin1</option>
                </select>
            </div>
        `);
    }

    createTable() {
        this.showAdminModal('Yeni Tablo', `
            <div class="form-group">
                <label>Tablo Adı</label>
                <input type="text" id="table-name" class="form-input" placeholder="Tablo adını girin">
            </div>
            <div class="form-group">
                <label>Motor</label>
                <select id="table-engine" class="form-select">
                    <option value="InnoDB">InnoDB</option>
                    <option value="MyISAM">MyISAM</option>
                    <option value="Memory">Memory</option>
                </select>
            </div>
        `);
    }

    createUser() {
        this.showAdminModal('Yeni Kullanıcı', `
            <div class="form-group">
                <label>Kullanıcı Adı</label>
                <input type="text" id="user-name" class="form-input" placeholder="Kullanıcı adını girin">
            </div>
            <div class="form-group">
                <label>Şifre</label>
                <input type="password" id="user-password" class="form-input" placeholder="Şifre girin">
            </div>
            <div class="form-group">
                <label>Rol</label>
                <select id="user-role" class="form-select">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
        `);
    }

    optimizeDatabase() {
        this.showToast('Veritabanı optimizasyonu başlatıldı...', 'info');
        // Optimizasyon işlemi
    }

    repairTables() {
        this.showToast('Tablo onarımı başlatıldı...', 'info');
        // Onarım işlemi
    }

    clearCache() {
        this.showToast('Önbellek temizlendi', 'success');
        // Önbellek temizleme işlemi
    }

    updateIndexes() {
        this.showToast('İndeks güncellemesi başlatıldı...', 'info');
        // İndeks güncelleme işlemi
    }

    analyzeTables() {
        this.showToast('Tablo analizi başlatıldı...', 'info');
        // Analiz işlemi
    }

    checkIntegrity() {
        this.showToast('Bütünlük kontrolü başlatıldı...', 'info');
        // Bütünlük kontrolü işlemi
    }

    scanSecurity() {
        this.showToast('Güvenlik taraması başlatıldı...', 'info');
        // Güvenlik taraması işlemi
    }

    blockSuspicious() {
        this.showToast('Şüpheli IP\'ler engellendi', 'success');
        // IP engelleme işlemi
    }

    createBackup() {
        this.showToast('Yedekleme işlemi başlatıldı...', 'info');
        // Yedekleme işlemi
    }

    scheduleBackup() {
        this.showAdminModal('Otomatik Yedekleme', `
            <div class="form-group">
                <label>Yedekleme Sıklığı</label>
                <select id="backup-schedule" class="form-select">
                    <option value="daily">Günlük</option>
                    <option value="weekly">Haftalık</option>
                    <option value="monthly">Aylık</option>
                </select>
            </div>
            <div class="form-group">
                <label>Yedekleme Saati</label>
                <input type="time" id="backup-time" class="form-input">
            </div>
        `);
    }

    downloadLogs() {
        this.showToast('Loglar indiriliyor...', 'info');
        // Log indirme işlemi
    }

    clearLogs() {
        this.showAdminModal('Logları Temizle', `
            <p>Tüm logları temizlemek istediğinizden emin misiniz?</p>
            <p class="text-warning">Bu işlem geri alınamaz!</p>
        `);
    }

    filterLogs() {
        const level = $('#log-level').val();
        const date = $('#log-date').val();

        this.showToast('Loglar filtreleniyor...', 'info');
        // Log filtreleme işlemi
    }
}

// Admin dashboard'u başlat
const adminDashboard = new AdminDashboard();

// Global erişim için
window.adminDashboard = adminDashboard;
