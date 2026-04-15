import Swal from 'sweetalert2';

// Make SweetAlert available globally
window.Swal = Swal;

// Helper function for delete confirmations
window.confirmDelete = async function(message = 'Are you sure? This action cannot be undone.') {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    });
    return result.isConfirmed;
};

// Helper function for generic confirmations
window.confirm = async function(message) {
    const result = await Swal.fire({
        title: 'Confirm',
        text: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1f4e79',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    });
    return result.isConfirmed;
};

// Success notification
window.showSuccess = async function(title = 'Success!', message = '') {
    await Swal.fire({
        title: title,
        text: message,
        icon: 'success',
        confirmButtonColor: '#0f766e',
        timer: 3000,
        timerProgressBar: true
    });
};

// Error notification
window.showError = async function(title = 'Error!', message = '') {
    await Swal.fire({
        title: title,
        text: message,
        icon: 'error',
        confirmButtonColor: '#dc3545'
    });
};

// Info notification
window.showInfo = async function(title = 'Info', message = '') {
    await Swal.fire({
        title: title,
        text: message,
        icon: 'info',
        confirmButtonColor: '#1f4e79',
        timer: 3000,
        timerProgressBar: true
    });
};

// Display session status messages on page load
document.addEventListener('DOMContentLoaded', function() {
    const statusElement = document.querySelector('.session-status');
    if (statusElement) {
        const statusType = statusElement.dataset.type || 'success';
        const statusMessage = statusElement.textContent.trim();
        
        if (statusMessage) {
            const icon = statusType === 'error' ? 'error' : 'success';
            const color = statusType === 'error' ? '#dc3545' : '#0f766e';
            
            Swal.fire({
                title: statusType === 'error' ? 'Error!' : 'Success!',
                text: statusMessage,
                icon: icon,
                confirmButtonColor: color,
                timer: 4000,
                timerProgressBar: true
            });
        }
    }
});
