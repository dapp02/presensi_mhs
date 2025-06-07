// Variabel global untuk menyimpan mode dan ID saat ini
let currentModeMhs = 'add';
let currentIdMhs = null;

// Fungsi untuk memuat data mahasiswa dari database
function loadMahasiswaData() {
    fetch('../assets/js/mahasiswa_api.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displayMahasiswaData(data.data);
            } else {
                console.error('Error loading data:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
}

// Fungsi untuk menampilkan data mahasiswa di tabel
function displayMahasiswaData(mahasiswaList) {
    const tbody = document.querySelector('#mahasiswa-crud .crud-table tbody');
    tbody.innerHTML = '';
    
    mahasiswaList.forEach(mahasiswa => {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>${mahasiswa.nim}</td>
            <td>${mahasiswa.nama_lengkap}</td>
            <td>${mahasiswa.nama_prodi}</td>
            <td>${mahasiswa.nama_kelas}</td>
            <td>
                <button class="crud-button edit">Edit</button>
                <button class="crud-button delete">Hapus</button>
            </td>
        `;
        
        tbody.appendChild(newRow);
    });
}

// Fungsi untuk menambahkan data baru
function addMahasiswa(nim, nama, prodi, kelas, email) {
    const data = {
        action: 'add',
        nim: nim,
        nama: nama,
        prodi: prodi,
        kelas: kelas,
        email: email
    };
    
    fetch('../assets/js/mahasiswa_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            alert('Data mahasiswa berhasil ditambahkan!');
            loadMahasiswaData(); // Reload data setelah menambahkan
        } else {
            alert('Gagal menambahkan data: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menambahkan data');
    });
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
    
    currentModeMhs = mode;
    currentIdMhs = id;
    
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
    if (!modal) {
        console.error('Modal element not found!');
        return;
    }
    
    modal.style.display = 'none';
    modal.classList.remove('active');
    document.querySelector('#mahasiswa-modal .modal').classList.remove('active');
}

// Fungsi untuk mengisi form dengan data
function fillFormWithData(id) {
    // Cari data mahasiswa berdasarkan NIM
    fetch(`../assets/js/mahasiswa_api.php?nim=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.data) {
                const mahasiswa = data.data;
                document.getElementById('nim').value = mahasiswa.nim;
                document.getElementById('nama').value = mahasiswa.nama_lengkap;
                document.getElementById('prodi').value = mahasiswa.nama_prodi;
                document.getElementById('kelas').value = mahasiswa.nama_kelas;
                document.getElementById('email').value = mahasiswa.email;
            } else {
                console.error('Error loading mahasiswa data:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Fungsi untuk mengedit data mahasiswa
function editMahasiswa(nim, nama, prodi, kelas, email) {
    const data = {
        action: 'update',
        nim: nim,
        nama: nama,
        prodi: prodi,
        kelas: kelas,
        email: email
    };
    
    fetch('../assets/js/mahasiswa_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            alert('Data mahasiswa berhasil diperbarui!');
            loadMahasiswaData(); // Reload data setelah mengedit
        } else {
            alert('Gagal memperbarui data: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memperbarui data');
    });
}

// Fungsi untuk menghapus data mahasiswa
function deleteMahasiswa(nim) {
    if (confirm('Apakah Anda yakin ingin menghapus data mahasiswa ini?')) {
        const data = {
            action: 'delete',
            nim: nim
        };
        
        fetch('../assets/js/mahasiswa_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                alert('Data mahasiswa berhasil dihapus!');
                loadMahasiswaData(); // Reload data setelah menghapus
            } else {
                alert('Gagal menghapus data: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus data');
        });
    }
}

// Inisialisasi event listener
function initMahasiswaListeners() {
    // Event listener untuk tombol Tambah Mahasiswa
    const addButton = document.getElementById('add-mahasiswa');
    if (addButton) {
        addButton.addEventListener('click', () => showModalMahasiswa('add'));
    }
    
    // Event listener untuk tombol Edit dan Delete
    document.querySelector('#mahasiswa-crud .crud-table tbody').addEventListener('click', function(e) {
        if (e.target.classList.contains('edit')) {
            const row = e.target.closest('tr');
            const nim = row.cells[0].textContent;
            showModalMahasiswa('edit', nim);
        } else if (e.target.classList.contains('delete')) {
            const row = e.target.closest('tr');
            const nim = row.cells[0].textContent;
            deleteMahasiswa(nim);
        }
    });
    
    // Event listener untuk tombol Batal dan Close
    const cancelButton = document.getElementById('cancel-mahasiswa');
    const closeButton = document.getElementById('close-modal');
    if (cancelButton) cancelButton.addEventListener('click', hideModalMahasiswa);
    if (closeButton) closeButton.addEventListener('click', hideModalMahasiswa);
    
    // Event listener untuk klik di luar modal
    const modal = document.getElementById('mahasiswa-modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) hideModalMahasiswa();
        });
    }
    
    // Event listener untuk tombol simpan
    const saveButton = document.getElementById('save-mahasiswa');
    if (saveButton) {
        saveButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            const nim = document.getElementById('nim').value;
            const nama = document.getElementById('nama').value;
            const prodi = document.getElementById('prodi').value;
            const kelas = document.getElementById('kelas').value;
            const email = document.getElementById('email').value;
            
            if (!nim || !nama || !prodi || !kelas || !email) {
                alert('Semua field harus diisi!');
                return;
            }
            
            if (currentModeMhs === 'add') {
                addMahasiswa(nim, nama, prodi, kelas, email);
            } else if (currentModeMhs === 'edit') {
                editMahasiswa(nim, nama, prodi, kelas, email);
            }
            
            hideModalMahasiswa();
        });
    }
}

// Load data saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    loadMahasiswaData();
    initMahasiswaListeners();
});