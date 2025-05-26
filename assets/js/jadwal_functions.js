// Fungsi untuk menambahkan jadwal baru
function addJadwal(matkul, kelas, hari, jam, ruang) {
    const tbody = document.querySelector('#jadwal-crud .crud-table tbody');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>${matkul}</td>
        <td>${kelas}</td>
        <td>${hari}</td>
        <td>${jam}</td>
        <td>${ruang}</td>
        <td>
            <button class="crud-button edit">Edit</button>
            <button class="crud-button delete">Hapus</button>
        </td>
    `;
    
    tbody.appendChild(newRow);
    alert('Data jadwal berhasil ditambahkan!');
}

// Fungsi untuk mengedit jadwal
function editJadwal(row, matkul, kelas, hari, jam, ruang) {
    if (!row) {
        console.error('Row element tidak ditemukan');
        return;
    }
    
    const cells = row.querySelectorAll('td');
    cells[0].textContent = matkul;
    cells[1].textContent = kelas;
    cells[2].textContent = hari;
    cells[3].textContent = jam;
    cells[4].textContent = ruang;
    alert('Data jadwal berhasil diubah!');
}

// Fungsi untuk menghapus jadwal
function deleteJadwal(row) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        row.remove();
        alert('Data jadwal berhasil dihapus!');
    }
}

let currentModeJadwal = null;
let currentIdJadwal = null;

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
            const matkul = row.cells[0].textContent;
            showModalJadwal('edit', matkul);
        } else if (e.target.classList.contains('delete') && e.target.closest('#jadwal-crud')) {
            const row = e.target.closest('tr');
            deleteJadwal(row);
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
            // Perbaikan selector untuk mencari row yang akan diedit
            const rows = document.querySelectorAll('#jadwal-crud tr');
            let targetRow = null;
            
            for (const row of rows) {
                if (row.cells[0].textContent === currentIdJadwal) {
                    targetRow = row;
                    break;
                }
            }
            
            if (targetRow) {
                editJadwal(targetRow, matkul, kelas, hari, jam, ruangan);
            } else {
                alert('Data tidak ditemukan untuk diedit');
            }
        }
        
        hideModalJadwal();
    });
}

document.addEventListener('DOMContentLoaded', initJadwalListeners);