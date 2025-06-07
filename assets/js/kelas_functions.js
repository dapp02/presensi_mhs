// Variabel global untuk menyimpan mode dan ID saat ini
let currentModeKelas = null;
let currentIdKelas = null;

// Fungsi untuk memuat data kelas dari database
function loadKelasData() {
    fetch('../assets/js/kelas_api.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displayKelasData(data.data);
            } else {
                console.error('Error loading data:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
}

// Fungsi untuk menampilkan data kelas di tabel
function displayKelasData(kelasList) {
    const tbody = document.querySelector('#kelas-crud .crud-table tbody');
    tbody.innerHTML = '';
    
    kelasList.forEach(kelas => {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>${kelas.nama_kelas}</td>
            <td>${kelas.nama_kelas}</td>
            <td>${kelas.nama_prodi}</td>
            <td>${kelas.jumlah_mahasiswa}</td>
            <td>
                <button class="crud-button edit">Edit</button>
                <button class="crud-button delete">Hapus</button>
            </td>
        `;
        
        tbody.appendChild(newRow);
    });
}

// Fungsi untuk menambahkan kelas baru
function addKelas(kode, nama, prodi, jumlah) {
    const data = {
        action: 'add',
        kode: kode,
        nama: nama,
        prodi: prodi,
        jumlah: jumlah
    };
    
    fetch('../assets/js/kelas_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            alert('Data kelas berhasil ditambahkan!');
            loadKelasData(); // Reload data setelah menambahkan
        } else {
            alert('Gagal menambahkan data: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menambahkan data');
    });
}

// Fungsi untuk mengedit kelas
function editKelas(kode, nama, prodi, jumlah) {
    const data = {
        action: 'update',
        kode: kode,
        nama: nama,
        prodi: prodi,
        jumlah: jumlah
    };
    
    fetch('../assets/js/kelas_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            alert('Data kelas berhasil diperbarui!');
            loadKelasData(); // Reload data setelah mengedit
        } else {
            alert('Gagal memperbarui data: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memperbarui data');
    });
}

// Fungsi untuk menghapus kelas
function deleteKelas(kode) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        const data = {
            action: 'delete',
            kode: kode
        };
        
        fetch('../assets/js/kelas_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                alert('Data kelas berhasil dihapus!');
                loadKelasData(); // Reload data setelah menghapus
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
function initializeKelasEventListeners() {
    const addKelasButton = document.getElementById('add-kelas');
    const kelasForm = document.getElementById('kelas-form');
    const closeModalButtons = document.querySelectorAll('#kelas-modal .close-button, #cancel-kelas');
    const kelasModal = document.getElementById('kelas-modal');

    if (addKelasButton) {
        addKelasButton.addEventListener('click', () => showModalKelas('add'));
    }

    // Event delegation for Edit and Delete buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit') && e.target.closest('#kelas-crud')) {
            const row = e.target.closest('tr');
            const kode = row.cells[0].textContent;
            showModalKelas('edit', kode);
        }
        
        if (e.target.classList.contains('delete') && e.target.closest('#kelas-crud')) {
            const row = e.target.closest('tr');
            const kode = row.cells[0].textContent;
            deleteKelas(kode);
        }
    });
    
    if (kelasForm) {
        kelasForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const kode = document.getElementById('kode-kelas').value;
            const nama = document.getElementById('nama-kelas').value;
            const prodi = document.getElementById('prodi-kelas').value;
            const jumlah = document.getElementById('jumlah-mahasiswa').value;
            
            if (currentModeKelas === 'add') {
                addKelas(kode, nama, prodi, jumlah);
            } else if (currentModeKelas === 'edit') {
                editKelas(kode, nama, prodi, jumlah);
            }
            hideModalKelas();
        });
    }
    
    closeModalButtons.forEach(button => {
        button.addEventListener('click', hideModalKelas);
    });
    
    if (kelasModal) {
        window.addEventListener('click', function(event) {
            if (event.target === kelasModal) {
                hideModalKelas();
            }
        });
    }
}

// Panggil fungsi inisialisasi saat DOM selesai dimuat
document.addEventListener('DOMContentLoaded', function() {
    const kelasCrudSection = document.getElementById('kelas-crud');
    if (kelasCrudSection) {
        loadKelasData();
        initializeKelasEventListeners();
    }
});