// assets/js/main.js - JavaScript chung cho toàn bộ ứng dụng

// Format số tiền VND
function formatMoney(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

// Format ngày tháng
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN');
}

// Tính số ngày giữa 2 ngày
function calculateDays(startDate, endDate, startSession = 'ca_ngay', endSession = 'ca_ngay') {
    const start = new Date(startDate);
    const end = new Date(endDate);
    const diffTime = Math.abs(end - start);
    let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
    
    // Trừ nửa ngày nếu cần
    if (startSession === 'buoi_chieu') diffDays -= 0.5;
    if (endSession === 'buoi_sang') diffDays -= 0.5;
    
    return Math.max(0, diffDays);
}

// Cập nhật số ngày nghỉ tự động
function updateLeaveDays() {
    const startDate = document.getElementById('ngay_bat_dau')?.value;
    const endDate = document.getElementById('ngay_ket_thuc')?.value;
    const startSession = document.getElementById('buoi_bat_dau')?.value || 'ca_ngay';
    const endSession = document.getElementById('buoi_ket_thuc')?.value || 'ca_ngay';
    const daysDisplay = document.getElementById('so_ngay_nghi_display');
    
    if (startDate && endDate && daysDisplay) {
        const days = calculateDays(startDate, endDate, startSession, endSession);
        daysDisplay.textContent = days.toFixed(1);
    }
}

// Confirm dialog
function confirmAction(message) {
    return confirm(message || 'Bạn có chắc chắn muốn thực hiện hành động này?');
}

// Show loading
function showLoading() {
    const loadingHTML = `
        <div class="loading-overlay" id="loadingOverlay">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', loadingHTML);
}

// Hide loading
function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) overlay.remove();
}

// Toast notification
function showToast(message, type = 'success') {
    const toastHTML = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(container);
    }
    
    container.insertAdjacentHTML('beforeend', toastHTML);
    const toastElement = container.lastElementChild;
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

// DataTable initialization
function initDataTable(tableId, options = {}) {
    if (typeof $.fn.DataTable !== 'undefined') {
        const defaultOptions = {
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/vi.json'
            },
            pageLength: 10,
            responsive: true,
            order: [[0, 'desc']]
        };
        
        return $(`#${tableId}`).DataTable({...defaultOptions, ...options});
    }
}

// Validate form
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return false;
    }
    
    return true;
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Đã sao chép vào clipboard', 'success');
    }).catch(err => {
        showToast('Không thể sao chép', 'danger');
    });
}

// Export table to Excel
function exportTableToExcel(tableId, filename = 'export.xlsx') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    // Logic export - cần thêm thư viện như SheetJS
    showToast('Chức năng đang được phát triển', 'info');
}

// Print page
function printPage() {
    window.print();
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Thêm style cho loading overlay
    const style = document.createElement('style');
    style.textContent = `
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
    `;
    document.head.appendChild(style);
});

// AJAX helper
function ajaxRequest(url, method = 'GET', data = null) {
    return new Promise((resolve, reject) => {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            }
        };
        
        if (data && method !== 'GET') {
            options.body = JSON.stringify(data);
        }
        
        fetch(url, options)
            .then(response => response.json())
            .then(data => resolve(data))
            .catch(error => reject(error));
    });
}