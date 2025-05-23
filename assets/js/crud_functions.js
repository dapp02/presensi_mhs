// Variabel untuk menyimpan mode (tambah/edit) dan ID yang sedang diedit
let currentMode = 'add';
let currentId = null;

// Fungsi untuk mengubah menu aktif
function changeActiveMenu(menuId) {
    // Pastikan menuId valid
    const validMenus = ['mahasiswa', 'kelas', 'matakuliah', 'jadwal'];
    if (!validMenus.includes(menuId)) return;
    
    // Sembunyikan semua konten CRUD dan nonaktifkan semua menu
    document.querySelectorAll('.crud-content').forEach(content => {
        content.style.display = 'none';
        content.classList.remove('active');
    });
    
    document.querySelectorAll('.crud-menu-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Tampilkan konten yang dipilih dan aktifkan menu item
    const targetContent = document.getElementById(menuId + '-crud');
    const targetMenuItem = document.querySelector(`.crud-menu-item[data-target="${menuId}"]`);
    
    if (targetContent && targetMenuItem) {
        targetContent.style.display = 'block';
        targetContent.classList.add('active');
        targetMenuItem.classList.add('active');
        
        // Tambahkan timeout untuk memastikan transisi CSS berjalan
        setTimeout(() => {
            targetContent.style.opacity = 1;
        }, 10);
    }
}

// Inisialisasi event listener untuk menu
function initMenuListeners() {
    document.querySelectorAll('.crud-menu-item').forEach(item => {
        item.addEventListener('click', function() {
            // Tutup semua modal saat berpindah menu
            document.querySelectorAll('.modal-overlay').forEach(modal => {
                modal.style.display = 'none';
                modal.classList.remove('active');
            });
            
            // Update menu aktif
            const target = this.getAttribute('data-target');
            changeActiveMenu(target);
        });
    });
}

document.addEventListener('DOMContentLoaded', initMenuListeners);

// Inisialisasi saat halaman dimuat
function initCRUD() {
    // Inisialisasi menu pertama kali
    changeActiveMenu('mahasiswa');
    
    // Set up event listeners
    initMenuListeners();
}

document.addEventListener('DOMContentLoaded', initCRUD);


// Fungsi untuk menampilkan modal
function showModal(mode, id = null) {
    const modal = document.getElementById('mahasiswa-modal');
    const modalTitle = document.getElementById('modal-title');
    
    document.getElementById('mahasiswa-form').reset();
    
    currentMode = mode;
    currentId = id;
    
    if (mode === 'add') {
        modalTitle.textContent = 'Tambah Mahasiswa Baru';
    } else if (mode === 'edit') {
        modalTitle.textContent = 'Edit Data Mahasiswa';
        fillFormWithData(id);
    }
    
    modal.classList.add('active');
    document.querySelector('#mahasiswa-modal .modal').classList.add('active');
}

// Fungsi untuk menyembunyikan modal
function hideModal() {
    const modal = document.getElementById('mahasiswa-modal');
    modal.classList.remove('active');
    document.querySelector('#mahasiswa-modal .modal').classList.remove('active');
}

// Fungsi untuk mengisi form dengan data
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
    document.getElementById('prodi').value = mahasiswa.prodi;
    document.getElementById('kelas').value = mahasiswa.kelas;
    document.getElementById('email').value = mahasiswa.email;
}

// Inisialisasi event listener
function initCRUDListeners() {
    // Hapus event listener sebelumnya jika ada
    document.getElementById('add-mahasiswa').removeEventListener('click', arguments.callee);
    document.getElementById('cancel-mahasiswa').removeEventListener('click', hideModal);
    document.getElementById('close-modal').removeEventListener('click', hideModal);
    document.getElementById('mahasiswa-modal').removeEventListener('click', arguments.callee);
    document.getElementById('save-mahasiswa').removeEventListener('click', arguments.callee);

    // Event listener untuk tombol Tambah Mahasiswa
    document.getElementById('add-mahasiswa').addEventListener('click', function() {
        showModal('add');
    });
    
    // Event listener untuk tombol Edit
    const editButtons = document.querySelectorAll('.crud-button.edit');
    editButtons.forEach(button => {
        button.removeEventListener('click', arguments.callee);
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const nim = row.cells[0].textContent;
            showModal('edit', nim);
        });
    });
    
    // Event listener untuk tombol Batal
    document.getElementById('cancel-mahasiswa').addEventListener('click', hideModal);
    
    // Event listener untuk tombol Close (X)
    document.getElementById('close-modal').addEventListener('click', hideModal);
    
    // Event listener untuk klik di luar modal
    document.getElementById('mahasiswa-modal').addEventListener('click', function(e) {
        if (e.target === this) hideModal();
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
        
        hideModal();
    });
}

document.addEventListener('DOMContentLoaded', initCRUD);