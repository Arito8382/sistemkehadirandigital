// Form Validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formKehadiran');
    if (!form) return;

    // NIK Validation
    const nikInput = document.getElementById('nik');
    if (nikInput) {
        nikInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 16) {
                this.value = this.value.slice(0, 16);
            }
        });

        nikInput.addEventListener('blur', function() {
            if (this.value.length > 0 && this.value.length !== 16) {
                this.setCustomValidity('NIK harus 16 digit');
                this.reportValidity();
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // Phone Number Validation
    const hpInput = document.getElementById('no_hp');
    if (hpInput) {
        hpInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 13) {
                this.value = this.value.slice(0, 13);
            }
        });

        hpInput.addEventListener('blur', function() {
            if (this.value.length > 0 && this.value.length < 10) {
                this.setCustomValidity('Nomor HP minimal 10 digit');
                this.reportValidity();
            } else if (this.value.length > 0 && !this.value.startsWith('0')) {
                this.setCustomValidity('Nomor HP harus diawali dengan 0');
                this.reportValidity();
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // File Upload Validation
    const fotoInput = document.getElementById('foto');
    if (fotoInput) {
        fotoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Check file size (max 2MB)
                if (file.size > 2097152) {
                    alert('Ukuran file maksimal 2MB');
                    this.value = '';
                    return;
                }

                // Check file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file harus JPG, JPEG, atau PNG');
                    this.value = '';
                    return;
                }
            }
        });
    }

    // Form Submit Confirmation
    form.addEventListener('submit', function(e) {
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    });
});