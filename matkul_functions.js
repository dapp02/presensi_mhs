// Variabel untuk menyimpan mode dan ID
let currentModeMatkul = 'add';
let currentIdMatkul = null;

// Fungsi untuk menambahkan mata kuliah baru
function addMatkul(kode, nama, sks, semester) {
    // Ubah selector dari #matkul-crud menjadi #matakuliah-crud
    let table = document.querySelector('#matakuliah-crud table');
    
    // If table doesn't exist, create it
    if (!table) {
        const crudContainer = document.querySelector('#matakuliah-crud');
        if (!crudContainer) {
            console.error('CRUD container not found');
            return;
        }
        
        // Create table structure
        table = document.createElement('table');
        table.className = 'crud-table';
        table.innerHTML = `
            <thead>
                <tr>
                    <th>Kode MK</th>
                    <th>Nama Mata Kuliah</th>
                    <th>SKS</th>
                    <th>Semester</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        `;
        crudContainer.appendChild(table);
    }
    
    let tbody = table.querySelector('tbody');
    if (!tbody) {
        tbody = document.createElement('tbody');
        table.appendChild(tbody);
    }
    
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>${kode}</td>
        <td>${nama}</td>
        <td>${sks}</td>
        <td>${semester}</td>
        <td>
            <button class="crud-button edit">Edit</button>
            <button class="crud-button delete">Hapus</button>
        </td>
    `;
    
    tbody.appendChild(newRow);
    alert('Data mata kuliah berhasil ditambahkan!');
}

// Fungsi untuk mengedit mata kuliah
function editMatkul(kode, nama, sks, semester) {
    const rows = document.querySelectorAll('#matakuliah-crud table tbody tr');
    for (let row of rows) {
        if (row.cells[0].textContent === currentIdMatkul) {
            row.cells[0].textContent = kode;
            row.cells[1].textContent = nama;
            row.cells[2].textContent = sks;
            row.cells[3].textContent = semester;
            break;
        }
    }
    alert('Data mata kuliah berhasil diperbarui!');
}

// Fungsi untuk menghapus mata kuliah
function deleteMatkul(row) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        row.remove();
        alert('Data mata kuliah berhasil dihapus!');
    }
}

// Fungsi untuk menampilkan modal
function showModalMatkul(mode, id = null) {
    const modal = document.getElementById('matkul-modal');
    const modalContent = modal.querySelector('.modal');
    const modalTitle = document.getElementById('modal-title-matkul');
    
    document.getElementById('matkul-form').reset();
    currentModeMatkul = mode;
    currentIdMatkul = id;
    
    modalTitle.textContent = mode === 'add' ? 'Tambah Mata Kuliah Baru' : 'Edit Data Mata Kuliah';
    
    if (mode === 'edit') {
        fillFormWithDataMatkul(id);
    }
    
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.classList.add('active');
        modalContent.classList.add('active');
    }, 10);
}

function hideModalMatkul() {
    const modal = document.getElementById('matkul-modal');
    const modalContent = modal.querySelector('.modal');
    
    modal.classList.remove('active');
    modalContent.classList.remove('active');
    
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Fungsi untuk mengisi form dengan data
function fillFormWithDataMatkul(id) {
    const rows = document.querySelectorAll('#matakuliah-crud table tbody tr');
    for (let row of rows) {
        if (row.cells[0].textContent === id) {
            document.getElementById('kode-matkul').value = row.cells[0].textContent;
            document.getElementById('nama-matkul').value = row.cells[1].textContent;
            document.getElementById('sks').value = row.cells[2].textContent;
            document.getElementById('semester').value = row.cells[3].textContent;
            break;
        }
    }
}

// Inisialisasi event listener
function initMatkulListeners() {
    // Event listener untuk tombol tambah
    document.getElementById('add-matakuliah').addEventListener('click', () => showModalMatkul('add'));
    
    // Event delegation untuk tombol edit dan hapus
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit') && e.target.closest('#matakuliah-crud')) { // Ubah selector ke #matakuliah-crud
            const row = e.target.closest('tr');
            const kode = row.cells[0].textContent;
            showModalMatkul('edit', kode);
        }
        
        if (e.target.classList.contains('delete') && e.target.closest('#matakuliah-crud')) { // Ubah selector ke #matakuliah-crud
            const row = e.target.closest('tr');
            deleteMatkul(row);
        }
    });
    
    // Event listener untuk tombol simpan
    document.getElementById('save-matkul').addEventListener('click', function() {
        const kode = document.getElementById('kode-matkul').value;
        const nama = document.getElementById('nama-matkul').value;
        const sks = document.getElementById('sks').value;
        const semester = document.getElementById('semester').value;
        
        if (!kode || !nama || !sks || !semester) {
            alert('Semua field harus diisi!');
            return;
        }
        
        if (currentModeMatkul === 'add') {
            addMatkul(kode, nama, sks, semester);
        } else if (currentModeMatkul === 'edit') {
            editMatkul(kode, nama, sks, semester);
        }
        
        hideModalMatkul();
    });
    
    // Event listener untuk tombol close
    document.getElementById('close-modal-matkul').addEventListener('click', hideModalMatkul);
    
    // Event listener untuk tombol batal
    document.getElementById('cancel-matkul').addEventListener('click', hideModalMatkul);
    
    // Event listener untuk klik di luar modal
    document.getElementById('matkul-modal').addEventListener('click', function(e) {
        if (e.target === this) hideModalMatkul();
    });
}

document.querySelector('[data-target="matakuliah"]').addEventListener('click', function() {
    // Sembunyikan semua konten CRUD
    document.querySelectorAll('.crud-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Tampilkan konten mata kuliah
    document.getElementById('matakuliah-crud').classList.add('active');
    
    // Update menu aktif
    document.querySelectorAll('.crud-menu-item').forEach(item => {
        item.classList.remove('active');
    });
    this.classList.add('active');
});

// Tangani tombol tambah mata kuliah

document.addEventListener('DOMContentLoaded', function() {
    initMatkulListeners();
});