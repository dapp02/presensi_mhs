// Variabel untuk menyimpan mode dan ID
let currentModeJadwal = 'add';
let currentIdJadwal = null;

// Fungsi untuk memuat data jadwal dari database
function loadJadwalData() {
    fetch('../assets/js/jadwal_api.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displayJadwalData(data.data);
            } else {
                console.error('Error loading data:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
}

// Fungsi untuk menampilkan data jadwal di tabel
function displayJadwalData(jadwalList) {
    const tbody = document.querySelector('#jadwal-crud .crud-table tbody');
    if (!tbody) {
        console.error('Tabel jadwal tidak ditemukan');
        return;
    }
    
    // Clear existing rows
    tbody.innerHTML = '';
    
    // Add new rows from data
    jadwalList.forEach(jadwal => {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>${jadwal.kode_matkul}</td>
            <td>${jadwal.id_kelas}</td>
            <td>${jadwal.hari}</td>
            <td>${jadwal.jam}</td>
            <td>${jadwal.ruangan}</td>
            <td>
                <button class="crud-button edit">Edit</button>
                <button class="crud-button delete">Hapus</button>
            </td>
        `;
        
        tbody.appendChild(newRow);
    });
}

// Fungsi untuk menambahkan jadwal baru
function addJadwal(matkul, kelas, hari, jam, ruang) {
    const data = {
        action: 'add',
        matkul: matkul,
        kelas: kelas,
        hari: hari,
        jam: jam,
        ruang: ruang
    };
    
    fetch('../assets/js/jadwal_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            alert('Data jadwal berhasil ditambahkan!');
            loadJadwalData(); // Reload data setelah menambahkan
        } else {
            alert('Gagal menambahkan data: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menambahkan data');
    });
}

// Fungsi untuk mengedit jadwal
function editJadwal(id, matkul, kelas, hari, jam, ruang) {
    const data = {
        action: 'update',
        id: id,
        matkul: matkul,
        kelas: kelas,
        hari: hari,
        jam: jam,
        ruang: ruang
    };
    
    fetch('../assets/js/jadwal_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            alert('Data jadwal berhasil diubah!');
            loadJadwalData(); // Reload data setelah mengedit
        } else {
            alert('Gagal memperbarui data: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memperbarui data');
    });
}

// Fungsi untuk menghapus jadwal
function deleteJadwal(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        const data = {
            action: 'delete',
            id: id
        };
        
        fetch('../assets/js/jadwal_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                alert('Data jadwal berhasil dihapus!');
                loadJadwalData(); // Reload data setelah menghapus
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

// Fungsi untuk memuat data mata kuliah untuk dropdown
function loadMatkulDropdown() {
    fetch('../assets/js/jadwal_api.php?type=matkul')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                populateMatkulDropdown(data.data);
            } else {
                console.error('Error loading matkul data:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching matkul data:', error);
        });
}

// Fungsi untuk memuat data kelas untuk dropdown
function loadKelasDropdown() {
    fetch('../assets/js/jadwal_api.php?type=kelas')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                populateKelasDropdown(data.data);
            } else {
                console.error('Error loading kelas data:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching kelas data:', error);
        });
}

// Fungsi untuk mengisi dropdown mata kuliah
function populateMatkulDropdown(matkulList) {
    const dropdown = document.getElementById('matkul-jadwal');
    if (!dropdown) {
        console.error('Dropdown mata kuliah tidak ditemukan');
        return;
    }
    
    // Simpan opsi default
    const defaultOption = dropdown.options[0];
    
    // Kosongkan dropdown
    dropdown.innerHTML = '';
    
    // Tambahkan kembali opsi default
    dropdown.appendChild(defaultOption);
    
    // Tambahkan opsi dari data
    matkulList.forEach(matkul => {
        const option = document.createElement('option');
        option.value = matkul.kode_matkul;
        option.textContent = matkul.nama_matkul;
        dropdown.appendChild(option);
    });
}

// Fungsi untuk mengisi dropdown kelas
function populateKelasDropdown(kelasList) {
    const dropdown = document.getElementById('kelas-jadwal');
    if (!dropdown) {
        console.error('Dropdown kelas tidak ditemukan');
        return;
    }
    
    // Simpan opsi default
    const defaultOption = dropdown.options[0];
    
    // Kosongkan dropdown
    dropdown.innerHTML = '';
    
    // Tambahkan kembali opsi default
    dropdown.appendChild(defaultOption);
    
    // Tambahkan opsi dari data
    kelasList.forEach(kelas => {
        const option = document.createElement('option');
        option.value = kelas.id_kelas;
        option.textContent = `${kelas.nama_kelas} (${kelas.nama_prodi})`;
        dropdown.appendChild(option);
    });
}

function showModalJadwal(mode, id = null) {
    const modal = document.getElementById('jadwal-modal');
    if (!modal) {
        console.error('Modal jadwal tidak ditemukan!');
        return;
    }
    
    const modalTitle = document.getElementById('modal-title-jadwal');
    if (!modalTitle) {
        console.error('Judul modal jadwal tidak ditemukan!');
        return;
    }
    
    document.getElementById('jadwal-form').reset();
    
    // Load dropdown data
    loadMatkulDropdown();
    loadKelasDropdown();
    
    currentModeJadwal = mode;
    currentIdJadwal = id;
    
    if (mode === 'add') {
        modalTitle.textContent = 'Tambah Jadwal Baru';
    } else if (mode === 'edit') {
        modalTitle.textContent = 'Edit Data Jadwal';
        fillFormWithDataJadwal(id);
    }
    
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.classList.add('active');
        document.querySelector('#jadwal-modal .modal').classList.add('active');
    }, 10);
}

function hideModalJadwal() {
    const modal = document.getElementById('jadwal-modal');
    modal.classList.remove('active');
    document.querySelector('#jadwal-modal .modal').classList.remove('active');
    
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

function fillFormWithDataJadwal(id) {
    const rows = document.querySelectorAll('#jadwal-crud .crud-table tbody tr');
    for (let row of rows) {
        if (row.cells[0].textContent === id) {
            document.getElementById('matkul-jadwal').value = row.cells[0].textContent;
            document.getElementById('kelas-jadwal').value = row.cells[1].textContent;
            document.getElementById('hari').value = row.cells[2].textContent;
            document.getElementById('jam').value = row.cells[3].textContent;
            document.getElementById('ruangan').value = row.cells[4].textContent;
            break;
        }
    }
}

function initJadwalListeners() {
    // Event listener untuk tombol tambah
    document.getElementById('add-jadwal').addEventListener('click', () => showModalJadwal('add'));
    
    // Event delegation untuk tombol edit dan hapus
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit') && e.target.closest('#jadwal-crud')) {
            const row = e.target.closest('tr');
            const id = row.cells[0].textContent;
            showModalJadwal('edit', id);
        } else if (e.target.classList.contains('delete') && e.target.closest('#jadwal-crud')) {
            const row = e.target.closest('tr');
            const id = row.cells[0].textContent;
            deleteJadwal(id);
        }
    });
    
    // Event listener untuk tombol close
    document.getElementById('close-modal-jadwal').addEventListener('click', hideModalJadwal);
    
    // Event listener untuk tombol batal
    document.getElementById('cancel-jadwal').addEventListener('click', hideModalJadwal);
    
    // Event listener untuk klik di luar modal
    document.getElementById('jadwal-modal').addEventListener('click', function(e) {
        if (e.target === this) hideModalJadwal();
    });
    
    // Event listener untuk tombol simpan
    document.getElementById('save-jadwal').addEventListener('click', function() {
        const matkul = document.getElementById('matkul-jadwal').value;
        const kelas = document.getElementById('kelas-jadwal').value;
        const hari = document.getElementById('hari').value;
        const jam = document.getElementById('jam').value;
        const ruangan = document.getElementById('ruangan').value;
        
        if (!matkul || !kelas || !hari || !jam || !ruangan) {
            alert('Semua field harus diisi!');
            return;
        }
        
        if (currentModeJadwal === 'add') {
            addJadwal(matkul, kelas, hari, jam, ruangan);
        } else if (currentModeJadwal === 'edit') {
            editJadwal(currentIdJadwal, matkul, kelas, hari, jam, ruangan);
        }
        
        hideModalJadwal();
    });
}

document.addEventListener('DOMContentLoaded', function() {
    initJadwalListeners();
    loadJadwalData(); // Load data saat halaman dimuat
});