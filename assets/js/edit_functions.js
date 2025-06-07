// Fungsi untuk mengedit data mahasiswa
function editMahasiswa(nim, nama, prodi, kelas, email) {
    const rows = document.querySelectorAll('.crud-table tbody tr');
    for (let row of rows) {
        if (row.cells[0].textContent === currentId) {
            row.cells[0].textContent = nim;
            row.cells[1].textContent = nama;
            row.cells[2].textContent = prodi === 'TI' ? 'Teknik Informatika' : (prodi === 'SI' ? 'Sistem Informasi' : 'Teknologi Rekayasa Perangkat Lunak');
            row.cells[3].textContent = kelas;
            break;
        }
    }
    alert('Data mahasiswa berhasil diperbarui!');
}

// Fungsi untuk mengedit data kelas
function editKelas(kode, nama, prodi, jumlah) {
    const rows = document.querySelectorAll('#kelas-crud .crud-table tbody tr');
    for (let row of rows) {
        if (row.cells[0].textContent === currentId) {
            row.cells[0].textContent = kode;
            row.cells[1].textContent = nama;
            row.cells[2].textContent = prodi;
            row.cells[3].textContent = jumlah;
            break;
        }
    }
    alert('Data kelas berhasil diperbarui!');
}