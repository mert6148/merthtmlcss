/**
 * User Panel JavaScript
 */
class UserDashboard extends DatabaseDashboard {
    constructor() {
        super();
        this.currentSection = 'overview';
        this.favoritesOnly = false;
        this.initUser();
    }

    initUser() {
        this.bindUserEvents();
        this.loadUserData();
        this.applyTheme(this.config.theme || 'light');
        console.log('User Dashboard initialized');
    }

    bindUserEvents() {
        $(document).on('click', '.nav-link', (e) => {
            e.preventDefault();
            const target = $(e.currentTarget).attr('href').substring(1);
            this.switchSection(target);
        });
        $(document).on('click', '#export-all', () => this.exportAll());
        $(document).on('click', '#refresh-tables', () => this.loadUserTables());
        $(document).on('click', '#toggle-favorites', () => {
            this.favoritesOnly = !this.favoritesOnly;
            $('#toggle-favorites span').text(this.favoritesOnly ? 'Tümünü Göster' : 'Favorileri Göster');
            this.loadUserTables();
        });
        $(document).on('input', '#user-table-search', (e) => this.filterTables(e.target.value));
    }

    switchSection(sectionId) {
        $('.user-section').addClass('hidden');
        $(`#${sectionId}`).removeClass('hidden');
        $('.nav-item').removeClass('active');
        $(`.nav-link[href="#${sectionId}"]`).parent().addClass('active');
        this.currentSection = sectionId;
        this.loadSectionData(sectionId);
    }

    loadSectionData(sectionId) {
        switch (sectionId) {
            case 'overview':
                this.loadUserData();
                break;
            case 'my-tables':
                this.loadUserTables();
                break;
            case 'activity':
                this.loadRecentActivity();
                break;
        }
    }

    async loadUserData() {
        try {
            this.showLoading(true);
            const [status, stats] = await Promise.all([
                this.checkConnection(),
                this.getDatabaseStats()
            ]);
            this.updateConnectionStatus(status);
            this.updateUserStats(stats);
            await this.loadUserTables();
            await this.loadRecentActivity();
        } catch (err) {
            console.error(err);
            this.showToast('Veriler yüklenemedi', 'error');
        } finally {
            this.showLoading(false);
        }
    }

    updateUserStats(stats) {
        $('#my-tables-count').text(stats.tables || 0);
        $('#favorites-count').text((stats.favorites && stats.favorites.length) || 0);
        $('#recent-queries').text((stats.recent_queries && stats.recent_queries.length) || 0);
        $('#exports-count').text(stats.exports || 0);
    }

    async loadUserTables() {
        try {
            let tables = await this.getTables();
            tables = (tables || []).filter(t => t.owner === 'me' || t.is_shared === true);
            if (this.favoritesOnly) tables = tables.filter(t => !!t.favorite);
            this.updateUserTablesList(tables);
        } catch (err) {
            console.error(err);
            this.showToast('Tablolar yüklenemedi', 'error');
        }
    }

    updateUserTablesList(tables) {
        const container = $('#user-tables-list');
        container.empty();
        if (!tables || tables.length === 0) {
            container.html('<div class="no-data">Tablo bulunamadı</div>');
            return;
        }
        tables.forEach(t => {
            const item = $(`
                <div class="user-table-item">
                    <div class="table-header-user">
                        <h4 class="table-name-user">${t.name}</h4>
                        <div class="table-actions-user">
                            <button class="btn btn-sm btn-secondary" onclick="userDashboard.viewTable('${t.name}')"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-sm btn-primary" onclick="userDashboard.exportTable('${t.name}')"><i class="fas fa-download"></i></button>
                            <button class="btn btn-sm ${t.favorite ? 'btn-warning' : 'btn-secondary'}" onclick="userDashboard.toggleFavorite('${t.name}')"><i class="fas fa-star"></i></button>
                        </div>
                    </div>
                    <div class="table-info">
                        <div class="info-item"><i class="fas fa-database"></i><span>Boyut: ${this.formatBytes(t.size)}</span></div>
                        <div class="info-item"><i class="fas fa-list"></i><span>Kayıt: ${this.formatNumber(t.records)}</span></div>
                        <div class="info-item"><i class="fas fa-user"></i><span>Sahip: ${t.owner || 'ben'}</span></div>
                    </div>
                </div>
            `);
            container.append(item);
        });
    }

    filterTables(term) {
        const q = (term || '').toLowerCase();
        $('#user-tables-list .user-table-item').each(function () {
            const name = $(this).find('.table-name-user').text().toLowerCase();
            $(this).toggle(name.includes(q));
        });
    }

    async viewTable(tableName) {
        this.showModal('Tablo Önizleme', `
            <div class="table-preview">
                <div class="loading"><div class="spinner"></div><span>Örnek veriler yükleniyor...</span></div>
            </div>
        `, [{ text: 'Kapat', class: 'btn-secondary', action: () => this.hideModal() }]);
        try {
            const resp = await fetch(`/api/database/sample/${tableName}`);
            const data = await resp.json();
            const html = this.renderPreviewTable(data.columns || [], data.rows || []);
            $('.table-preview').html(html);
        } catch (err) {
            $('.table-preview').html('<div class="no-data">Örnek veriler alınamadı</div>');
        }
    }

    renderPreviewTable(columns, rows) {
        if (!columns.length) return '<div class="no-data">Sütun bilgisi yok</div>';
        const thead = '<thead><tr>' + columns.map(c => `<th>${c}</th>`).join('') + '</tr></thead>';
        const tbody = '<tbody>' + rows.map(r => `<tr>${columns.map(c => `<td>${r[c]}</td>`).join('')}</tr>`).join('') + '</tbody>';
        return `<div class="table-responsive"><table class="table">${thead}${tbody}</table></div>`;
    }

    async exportAll() {
        try {
            this.showToast('Dışa aktarma hazırlanıyor...', 'info');
            const resp = await fetch('/api/database/export/all');
            const blob = await resp.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `export_${new Date().toISOString().split('T')[0]}.zip`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
            this.showToast('Dışa aktarma tamamlandı', 'success');
        } catch (err) {
            this.showToast('Dışa aktarma başarısız', 'error');
        }
    }

    async toggleFavorite(tableName) {
        try {
            await fetch(`/api/database/favorite/${tableName}`, { method: 'POST' });
            this.loadUserTables();
        } catch (_) { this.showToast('Favori değiştirilemedi', 'error'); }
    }

    async loadRecentActivity() {
        try {
            const resp = await fetch('/api/database/activity');
            const list = await resp.json();
            this.updateRecentActivity(list || []);
        } catch (_) {
            this.updateRecentActivity([]);
        }
    }

    updateRecentActivity(items) {
        const container = $('#recent-activity');
        container.empty();
        if (!items.length) {
            container.html('<div class="no-data">Henüz aktivite yok</div>');
            return;
        }
        items.forEach(x => {
            const el = $(`
                <div class="activity-item">
                    <i class="fas ${this.iconForActivity(x.type)}"></i>
                    <div class="activity-text">${x.message}</div>
                    <div class="activity-time">${this.formatDate(x.timestamp)}</div>
                </div>
            `);
            container.append(el);
        });
    }

    iconForActivity(type) {
        const map = { query: 'fa-terminal', export: 'fa-download', view: 'fa-eye', favorite: 'fa-star' };
        return map[type] || 'fa-info-circle';
    }
}

const userDashboard = new UserDashboard();
window.userDashboard = userDashboard;


