// Admin Panel JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Dark Mode Toggle di Topbar
    const darkModeBtn = document.getElementById('darkModeBtn');
    const body = document.body;

    // Load saved dark mode preference
    if (localStorage.getItem('darkMode') === 'enabled') {
        body.classList.add('dark-mode');
        if (darkModeBtn) {
            darkModeBtn.querySelector('i').classList.replace('fa-moon', 'fa-sun');
        }
    }

    if (darkModeBtn) {
        darkModeBtn.addEventListener('click', function() {
            body.classList.toggle('dark-mode');
            const icon = this.querySelector('i');
            
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
                icon.classList.replace('fa-moon', 'fa-sun');
            } else {
                localStorage.setItem('darkMode', 'disabled');
                icon.classList.replace('fa-sun', 'fa-moon');
            }
        });
    }

    // Sidebar Toggle
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileToggle = document.getElementById('mobileToggle');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
    }

    // Load sidebar state
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
    }

    // Mobile Toggle
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('mobile-open');
        });
    }

    // Close sidebar on mobile when clicking menu item
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('mobile-open');
            }
        });
    });

    // Close sidebar on mobile when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !mobileToggle.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
            }
        }
    });

    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert, .alert-success, .alert-error');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // Confirm Delete
    const deleteButtons = document.querySelectorAll('a[href*="delete.php"]');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Yakin ingin menghapus data ini?')) {
                e.preventDefault();
            }
        });
    });

    // Real-time Search in Table
    const searchInput = document.querySelector('.search-box input[type="text"]');
    if (searchInput && !searchInput.form) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = this.value.toLowerCase();
            
            searchTimeout = setTimeout(() => {
                const tableRows = document.querySelectorAll('.data-table tbody tr');
                let visibleCount = 0;

                tableRows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                        visibleCount++;
                        // Highlight effect
                        if (searchTerm && searchTerm.length > 2) {
                            row.style.backgroundColor = '#fef3c7';
                            setTimeout(() => {
                                row.style.backgroundColor = '';
                            }, 1000);
                        }
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Show "no results" message
                const noResultsRow = document.querySelector('.no-results-row');
                if (visibleCount === 0 && searchTerm) {
                    if (!noResultsRow) {
                        const tbody = document.querySelector('.data-table tbody');
                        const colspan = document.querySelectorAll('.data-table thead th').length;
                        const newRow = document.createElement('tr');
                        newRow.className = 'no-results-row';
                        newRow.innerHTML = `<td colspan="${colspan}" class="text-center" style="padding: 2rem; color: #6b7280;">
                            <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i><br>
                            Tidak ada hasil untuk "${searchTerm}"
                        </td>`;
                        tbody.appendChild(newRow);
                    }
                } else if (noResultsRow) {
                    noResultsRow.remove();
                }
            }, 300);
        });
    }

    // Bar Chart Animation
    const bars = document.querySelectorAll('.bar');
    if (bars.length > 0) {
        const observerOptions = {
            threshold: 0.5
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'growBar 0.8s ease-out forwards';
                }
            });
        }, observerOptions);

        bars.forEach(bar => observer.observe(bar));
    }

    // Stats Animation (Count Up)
    const statValues = document.querySelectorAll('.stat-value');
    const hasAnimated = new Set();
    
    const animateValue = (element, start, end, duration) => {
        if (hasAnimated.has(element)) return;
        hasAnimated.add(element);
        
        let startTime = null;
        const step = (timestamp) => {
            if (!startTime) startTime = timestamp;
            const progress = Math.min((timestamp - startTime) / duration, 1);
            const value = Math.floor(progress * (end - start) + start);
            element.textContent = value;
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    };

    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const finalValue = parseInt(entry.target.textContent);
                animateValue(entry.target, 0, finalValue, 1000);
            }
        });
    });

    statValues.forEach(stat => statsObserver.observe(stat));

    // Print Function
    window.printPage = function() {
        window.print();
    };

    // Export Functions
    window.exportExcel = function() {
        const searchParams = new URLSearchParams(window.location.search);
        window.location.href = 'export_excel.php?' + searchParams.toString();
    };

    // Auto-refresh Dashboard
    if (window.location.pathname.includes('dashboard.php')) {
        if (localStorage.getItem('autoRefresh') === 'enabled') {
            setInterval(() => {
                location.reload();
            }, 60000); // 60 seconds
        }
    }

    // Smooth Scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Back to Top Button
    const backToTop = document.createElement('button');
    backToTop.innerHTML = '<i class="fas fa-arrow-up"></i>';
    backToTop.className = 'back-to-top';
    backToTop.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: none;
        z-index: 999;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        transition: all 0.3s;
    `;
    document.body.appendChild(backToTop);

    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            backToTop.style.display = 'block';
        } else {
            backToTop.style.display = 'none';
        }
    });

    backToTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Table Row Click (untuk mobile)
    if (window.innerWidth <= 768) {
        document.querySelectorAll('.data-table tbody tr').forEach(row => {
            row.style.cursor = 'pointer';
            row.addEventListener('click', function(e) {
                if (!e.target.closest('.action-buttons')) {
                    const detailLink = this.querySelector('a[href*="detail.php"]');
                    if (detailLink) {
                        window.location.href = detailLink.href;
                    }
                }
            });
        });
    }

    // Toast Notification Function
    window.showToast = function(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        `;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : '#ef4444'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            z-index: 9999;
            animation: slideIn 0.3s ease-out;
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    };
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes growBar {
        from {
            height: 0;
        }
        to {
            height: var(--final-height);
        }
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    .back-to-top:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.5);
    }

    body.dark-mode .back-to-top {
        background: #4f46e5;
    }
`;
document.head.appendChild(style);