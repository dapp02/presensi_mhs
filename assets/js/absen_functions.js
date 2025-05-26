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
        button.addEventListener('click', () => {
            selectAttendanceButton(button);
        });
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

    const saveButton = document.querySelector('.submit-btn');
    if (saveButton) {
        saveButton.addEventListener('click', () => {
            if (confirm('Apakah Anda yakin ingin menyimpan perubahan?')) {
                // Logika untuk menyimpan data bisa ditambahkan di sini
                // Untuk saat ini, kita hanya menampilkan pesan
                alert('Perubahan telah disimpan.');
                // Anda mungkin ingin mereset pilihan setelah menyimpan, atau menavigasi ke halaman lain
                // resetAttendanceSelection();
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