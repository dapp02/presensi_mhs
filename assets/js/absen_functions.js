function selectAttendanceButton(button) {
    // Dapatkan semua tombol dalam baris yang sama
    const row = button.closest('tr');
    const buttonsInRow = row.querySelectorAll('.action-button');

    // Hapus kelas 'selected' dari semua tombol di baris tersebut
    buttonsInRow.forEach(btn => {
        btn.classList.remove('selected');
    });

    // Tambahkan kelas 'selected' ke tombol yang diklik
    button.classList.add('selected');
}

function resetAttendanceSelection() {
    const allAttendanceButtons = document.querySelectorAll('.action-button');
    allAttendanceButtons.forEach(btn => {
        btn.classList.remove('selected');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const attendanceButtons = document.querySelectorAll('.action-button');
    attendanceButtons.forEach(button => {
        // Add click listener for dynamic selection
        button.addEventListener('click', () => {
            selectAttendanceButton(button);
        });

        // Check if the radio button inside is checked on load
        const radio = button.querySelector('input[type="radio"]');
        if (radio && radio.checked) {
            button.classList.add('selected');
        }
    });

    const cancelButton = document.querySelector('.cancel-btn');
    if (cancelButton) {
        cancelButton.addEventListener('click', () => {
            if (confirm('Apakah Anda yakin ingin membatalkan progres yang sudah ada?')) {
                resetAttendanceSelection();
                alert('Progres telah dibatalkan.');
            }
        });
    }

    const formAbsensi = document.getElementById('form-absensi');
    if (formAbsensi) {
        formAbsensi.addEventListener('submit', async (event) => {
            event.preventDefault(); // Mencegah pengiriman form secara default

            if (!confirm('Apakah Anda yakin ingin menyimpan perubahan absensi ini?')) {
                return; // Batalkan jika pengguna tidak yakin
            }

            const formData = new FormData(formAbsensi);

            try {
                const response = await fetch(formAbsensi.action, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    // Opsional: refresh halaman atau update UI setelah sukses
                    window.location.reload(); 
                } else {
                    alert('Gagal menyimpan absensi: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengirim data. Silakan coba lagi.');
            }
        });
    }
});

function markAttendance(type) {
    // Update status text
    document.getElementById('statusText').textContent = 'Kamu sudah Absen';
    
    // Hide the before attendance section and show the after attendance section
    document.getElementById('beforeAttendance').classList.add('hidden');
    document.getElementById('afterAttendance').classList.remove('hidden');
    
    // Reset all counts
    document.getElementById('absenCount').textContent = '0';
    document.getElementById('izinCount').textContent = '0';
    document.getElementById('sakitCount').textContent = '0';
    document.getElementById('alphaCount').textContent = '0';
    
    // Update the count for the selected attendance type
    document.getElementById(type + 'Count').textContent = '1';
    
    // Optionally, update the status icon
    // document.getElementById('statusIcon').src = "new-icon-url.png";
}