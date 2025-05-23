// Fungsi untuk menambahkan kelas baru
function addKelas(kode, nama, prodi, jumlah) {
    const tbody = document.querySelector('#kelas-crud .crud-table tbody');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>${kode}</td>
        <td>${nama}</td>
        <td>${prodi}</td>
        <td>${jumlah}</td>
        <td>
            <button class="crud-button edit">Edit</button>
            <button class="crud-button delete">Hapus</button>
        </td>
    `;
    
    tbody.appendChild(newRow);
    alert('Data kelas berhasil ditambahkan!');
}

// Fungsi untuk mengedit kelas
function editKelas(kode, nama, prodi, jumlah) {
    const rows = document.querySelectorAll('#kelas-crud .crud-table tbody tr');
    for (let row of rows) {
        if (row.cells[0].textContent === currentIdKelas) {
            row.cells[0].textContent = kode;
            row.cells[1].textContent = nama;
            row.cells[2].textContent = prodi;
            row.cells[3].textContent = jumlah;
            break;
        }
    }
    alert('Data kelas berhasil diperbarui!');
}

// Fungsi untuk menghapus kelas
function deleteKelas(row) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        row.remove();
        alert('Data kelas berhasil dihapus!');
    }
}

let currentModeKelas = null;
let currentIdKelas = null;

// Fungsi untuk menampilkan modal kelas
function showModalKelas(mode, id = null) {
    const modal = document.getElementById('kelas-modal');
    const modalTitle = document.getElementById('modal-title-kelas');
    
    document.getElementById('kelas-form').reset();
    
    currentModeKelas = mode;
    currentIdKelas = id;
    
    if (mode === 'add') {
        modalTitle.textContent = 'Tambah Kelas Baru';
    } else if (mode === 'edit') {
        modalTitle.textContent = 'Edit Data Kelas';
        fillFormWithDataKelas(id);
    }
    
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.classList.add('active');
        document.querySelector('#kelas-modal .modal').classList.add('active');
    }, 10);
}

// Fungsi untuk menyembunyikan modal
function hideModalKelas() {
    const modal = document.getElementById('kelas-modal');
    modal.classList.remove('active');
    document.querySelector('#kelas-modal .modal').classList.remove('active');
    
    setTimeout(() => {
        modal.style.display = 'none';
    }, 10);
}

// Fungsi untuk mengisi form dengan data
function fillFormWithDataKelas(id) {
    const rows = document.querySelectorAll('#kelas-crud .crud-table tbody tr');
    for (let row of rows) {
        if (row.cells[0].textContent === id) {
            document.getElementById('kode-kelas').value = row.cells[0].textContent;
            document.getElementById('nama-kelas').value = row.cells[1].textContent;
            document.getElementById('prodi-kelas').value = row.cells[2].textContent;
            document.getElementById('jumlah-mahasiswa').value = row.cells[3].textContent;
            break;
        }
    }
}

// Inisialisasi event listener
function initKelasListeners() {
    // Event listener untuk tombol Tambah Kelas
    document.getElementById('add-kelas').addEventListener('click', () => showModalKelas('add'));
    
    // Event delegation untuk tombol Edit
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit') && e.target.closest('#kelas-crud')) {
            const row = e.target.closest('tr');
            const kode = row.cells[0].textContent;
            showModalKelas('edit', kode);
        }
        
        // Event delegation untuk tombol Hapus
        if (e.target.classList.contains('delete') && e.target.closest('#kelas-crud')) {
            const row = e.target.closest('tr');
            deleteKelas(row);
        }
    });
    
    // Event listener untuk tombol Batal
    document.getElementById('cancel-kelas').addEventListener('click', hideModalKelas);
    
    // Event listener untuk tombol Close (X)
    document.getElementById('close-modal-kelas').addEventListener('click', hideModalKelas);
    
    // Event listener untuk klik di luar modal
    document.getElementById('kelas-modal').addEventListener('click', function(e) {
        if (e.target === this) hideModalKelas();
    });
    
    // Event listener untuk tombol simpan
    document.getElementById('save-kelas').addEventListener('click', function() {
        const kode = document.getElementById('kode-kelas').value;
        const nama = document.getElementById('nama-kelas').value;
        const prodi = document.getElementById('prodi-kelas').value;
        const jumlah = document.getElementById('jumlah-mahasiswa').value;
        
        if (!kode || !nama || !prodi || !jumlah) {
            alert('Semua field harus diisi!');
            return;
        }
        
        if (currentModeKelas === 'add') {
            addKelas(kode, nama, prodi, jumlah);
        } else if (currentModeKelas === 'edit') {
            editKelas(kode, nama, prodi, jumlah);
        }
        
        hideModalKelas();
    });
}

// Inisialisasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', initKelasListeners);