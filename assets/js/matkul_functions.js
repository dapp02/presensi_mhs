// Variabel untuk menyimpan mode dan ID
let currentModeMatkul = 'add';
let currentIdMatkul = null;

// DEFINISIKAN FUNGSI LOAD DI SCOPE GLOBAL
function loadMatkulData() {
    console.log("Memuat data mata kuliah..."); // Tambahkan log untuk debugging
    fetch('../assets/js/matkul_api.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displayMatkulData(data.data);
            } else {
                console.error('Error loading data:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
}

// Fungsi untuk menampilkan data mata kuliah di tabel
function displayMatkulData(matkulList) {
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
    
    // Clear existing rows
    tbody.innerHTML = '';
    
    // Add new rows from data
    matkulList.forEach(matkul => {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>${matkul.kode_matkul}</td>
            <td>${matkul.nama_matkul}</td>
            <td>${matkul.sks}</td>
            <td>${matkul.id_prodi}</td>
            <td>
                <button class="crud-button edit">Edit</button>
                <button class="crud-button delete">Hapus</button>
            </td>
        `;
        
        tbody.appendChild(newRow);
    });
}

// Fungsi untuk menambahkan mata kuliah baru
function addMatkul(kode, nama, sks, semester) {
    const data = {
        action: 'add',
        kode: kode,
        nama: nama,
        sks: sks,
        semester: semester
    };
    
    fetch('../assets/js/matkul_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            alert('Data mata kuliah berhasil ditambahkan!');
            loadMatkulData(); // Reload data setelah menambahkan
        } else {
            alert('Gagal menambahkan data: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menambahkan data');
    });
}

// Fungsi untuk mengedit mata kuliah
function editMatkul(kode, nama, sks, semester) {
    const data = {
        action: 'update',
        kode: kode,
        nama: nama,
        sks: sks,
        semester: semester
    };
    
    fetch('../assets/js/matkul_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            alert('Data mata kuliah berhasil diperbarui!');
            loadMatkulData(); // Reload data setelah mengedit
        } else {
            alert('Gagal memperbarui data: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memperbarui data');
    });
}

// Fungsi untuk menghapus mata kuliah
function deleteMatkul(kode) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        const data = {
            action: 'delete',
            kode: kode
        };
        
        fetch('../assets/js/matkul_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                alert('Data mata kuliah berhasil dihapus!');
                loadMatkulData(); // Reload data setelah menghapus
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
function initializeMatkulEventListeners() {
    const addMatkulButton = document.getElementById('add-matkul');
    const matkulForm = document.getElementById('matkul-form');
    const closeModalButtons = document.querySelectorAll('#matkul-modal .close-button, #cancel-matkul');
    const matkulModal = document.getElementById('matkul-modal');

    if (addMatkulButton) {
        addMatkulButton.addEventListener('click', () => showModalMatkul('add'));
    }

    // Event delegation for Edit and Delete buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit') && e.target.closest('#matakuliah-crud')) {
            const row = e.target.closest('tr');
            const kode = row.cells[0].textContent;
            showModalMatkul('edit', kode);
        }
        
        if (e.target.classList.contains('delete') && e.target.closest('#matakuliah-crud')) {
            const row = e.target.closest('tr');
            const kode = row.cells[0].textContent;
            deleteMatkul(kode);
        }
    });
    
    if (matkulForm) {
        matkulForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const kode = document.getElementById('kode-matkul').value;
            const nama = document.getElementById('nama-matkul').value;
            const sks = document.getElementById('sks-matkul').value;
            const semester = document.getElementById('semester-matkul').value;
            
            if (currentModeMatkul === 'add') {
                addMatkul(kode, nama, sks, semester);
            } else if (currentModeMatkul === 'edit') {
                editMatkul(kode, nama, sks, semester);
            }
            hideModalMatkul();
        });
    }
    
    closeModalButtons.forEach(button => {
        button.addEventListener('click', hideModalMatkul);
    });
    
    if (matkulModal) {
        window.addEventListener('click', function(event) {
            if (event.target === matkulModal) {
                hideModalMatkul();
            }
        });
    }
}

// Panggil fungsi inisialisasi saat DOM selesai dimuat
document.addEventListener('DOMContentLoaded', function() {
    const matkulCrudSection = document.getElementById('matakuliah-crud');
    if (matkulCrudSection) {
        loadMatkulData();
        initializeMatkulEventListeners();
    }
});