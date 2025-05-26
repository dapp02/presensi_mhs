// Fungsi untuk menambahkan data baru
function addMahasiswa(nim, nama, prodi, kelas, email) {
    const tbody = document.querySelector('.crud-table tbody');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>${nim}</td>
        <td>${nama}</td>
        <td>${prodi === 'TI' ? 'Teknik Informatika' : (prodi === 'SI' ? 'Sistem Informasi' : 'Teknologi Rekayasa Perangkat Lunak')}</td>
        <td>${kelas}</td>
        <td>
            <button class="crud-button edit">Edit</button>
            <button class="crud-button delete">Hapus</button>
        </td>
    `;
    
    tbody.appendChild(newRow);
    alert('Data mahasiswa berhasil ditambahkan!');
}

// Fungsi untuk menampilkan modal mahasiswa
function showModalMahasiswa(mode, id = null) {
    const modal = document.getElementById('mahasiswa-modal');
    if (!modal) {
        console.error('Modal element not found!');
        return;
    }
    
    const modalTitle = document.getElementById('modal-title');
    if (!modalTitle) {
        console.error('Modal title element not found!');
        return;
    }
    
    document.getElementById('mahasiswa-form').reset();
    
    currentMode = mode;
    currentId = id;
    
    if (mode === 'add') {
        modalTitle.textContent = 'Tambah Mahasiswa Baru';
    } else if (mode === 'edit') {
        modalTitle.textContent = 'Edit Data Mahasiswa';
        fillFormWithData(id);
    }
    
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.classList.add('active');
        document.querySelector('#mahasiswa-modal .modal').classList.add('active');
    }, 10);
}

// Fungsi untuk menyembunyikan modal mahasiswa
function hideModalMahasiswa() {
    const modal = document.getElementById('mahasiswa-modal');
    modal.classList.remove('active');
    document.querySelector('#mahasiswa-modal .modal').classList.remove('active');
    
    // Tambahkan ini untuk memastikan overlay benar-benar hilang
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300); // Sesuaikan dengan durasi transisi CSS
}

// Fungsi untuk mengisi form dengan data mahasiswa
function fillFormWithData(id) {
    let mahasiswa = {
        nim: id,
        nama: '',
        prodi: '',
        kelas: '',
        email: ''
    };

    const rows = document.querySelectorAll('.crud-table tbody tr');
    for (let row of rows) {
        if (row.cells[0].textContent === id) {
            mahasiswa.nama = row.cells[1].textContent;
            mahasiswa.prodi = row.cells[2].textContent;
            mahasiswa.kelas = row.cells[3].textContent;
            break;
        }
    }

    document.getElementById('nim').value = mahasiswa.nim;
    document.getElementById('nama').value = mahasiswa.nama;
    
    // Set nilai program studi dengan benar
    const prodiSelect = document.getElementById('prodi');
    if (prodiSelect) {
        // Hapus opsi pertama jika teksnya "Pilih Program Studi"
        if (prodiSelect.options.length > 0 && prodiSelect.options[0].text === 'Pilih Program Studi') {
            prodiSelect.remove(0);
        }
        
        for (let i = 0; i < prodiSelect.options.length; i++) {
            if (prodiSelect.options[i].value === mahasiswa.prodi) {
                prodiSelect.selectedIndex = i;
                break;
            }
        }
    }
    
    document.getElementById('kelas').value = mahasiswa.kelas;
    document.getElementById('email').value = mahasiswa.email;
}

// Inisialisasi event listener untuk mahasiswa
function initMahasiswaListeners() {
    // Event delegation untuk tombol edit dan hapus
     document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit') && e.target.closest('#mahasiswa-crud')) {
            const row = e.target.closest('tr');
            const nim = row.cells[0].textContent;
            showModalMahasiswa('edit', nim);
        } else if (e.target.classList.contains('delete') && e.target.closest('#mahasiswa-crud')) {
            const row = e.target.closest('tr');
            deleteMahasiswa(row);
        }
    });

    // Event listener untuk tombol tambah
    const addButton = document.getElementById('add-mahasiswa');
    if (addButton) {
        addButton.addEventListener('click', function() {
            showModalMahasiswa('add');
        });
    }
    
    // Event listener untuk tombol Batal
    document.getElementById('cancel-mahasiswa').addEventListener('click', hideModalMahasiswa);
    
    // Event listener untuk tombol Close (X)
    document.getElementById('close-modal').addEventListener('click', hideModalMahasiswa);
    
    // Event listener untuk klik di luar modal
    document.getElementById('mahasiswa-modal').addEventListener('click', function(e) {
        if (e.target === this) hideModalMahasiswa();
    });
    
    // Event listener untuk tombol simpan
    document.getElementById('save-mahasiswa').addEventListener('click', function saveHandler() {
        const nim = document.getElementById('nim').value;
        const nama = document.getElementById('nama').value;
        const prodi = document.getElementById('prodi').value;
        const kelas = document.getElementById('kelas').value;
        const email = document.getElementById('email').value;
        
        if (!nim || !nama || !prodi || !kelas || !email) {
            alert('Semua field harus diisi!');
            return;
        }
        
        if (currentMode === 'add') {
            addMahasiswa(nim, nama, prodi, kelas, email);
        } else if (currentMode === 'edit') {
            editMahasiswa(nim, nama, prodi, kelas, email);
        }
        
        hideModalMahasiswa();
    });
}

// Fungsi untuk mengedit data mahasiswa
function editMahasiswa(nim, nama, prodi, kelas, email) {
    const rows = document.querySelectorAll('#mahasiswa-crud .crud-table tbody tr');
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

// Fungsi untuk menghapus data mahasiswa
function deleteMahasiswa(row) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        row.remove();
        alert('Data mahasiswa berhasil dihapus!');
    }
}

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

// Tambahkan ini di akhir file
document.addEventListener('DOMContentLoaded', function() {
    initMahasiswaListeners();
});