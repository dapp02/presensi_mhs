window.initJadwal = function() {
     const jadwalContent = document.getElementById('jadwal-crud');
     if (jadwalContent && jadwalContent.dataset.listenersAttached === 'true') {
         window.loadJadwalData();
         return;
     }
     console.log("Menginisialisasi modul Jadwal untuk pertama kali...");
 
     // === DEKLARASI ELEMEN DOM === 
     const jadwalTbody = document.getElementById('jadwal-table-body');
     const modal = document.getElementById('jadwal-modal');
     const modalContent = modal.querySelector('.modal');
     const modalTitle = document.getElementById('jadwal-modal-title');
     const jadwalForm = document.getElementById('jadwal-form');
     const jadwalIdInput = document.getElementById('jadwal-id');
     const API_URL = '../App/Api/crud_jadwal_api.php';
 
     // === FUNGSI-FUNGSI === 
     let allJadwalData = []; // Variabel untuk menyimpan semua data jadwal

     window.loadJadwalData = function() {
         fetch(API_URL)
             .then(res => res.json())
             .then(res => {
                 if (res.success) {
                     allJadwalData = res.data; // Simpan data lengkap
                     filterJadwalTable(); // Tampilkan semua data awalnya
                 } else {
                     jadwalTbody.innerHTML = '<tr><td colspan="7">Tidak ada data jadwal.</td></tr>';
                 }
             }).catch(error => console.error('Error loading Jadwal data:', error));
     };

     function filterJadwalTable() {
         const searchInput = document.getElementById('jadwal-search-input');
         const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
         jadwalTbody.innerHTML = '';

         const filteredData = allJadwalData.filter(jadwal => {
             return (jadwal.nama_matkul && jadwal.nama_matkul.toLowerCase().includes(searchTerm)) ||
                    (jadwal.nama_dosen && jadwal.nama_dosen.toLowerCase().includes(searchTerm)) ||
                    (jadwal.nama_kelas && jadwal.nama_kelas.toLowerCase().includes(searchTerm)) ||
                    (jadwal.hari && jadwal.hari.toLowerCase().includes(searchTerm)) ||
                    (jadwal.ruangan && jadwal.ruangan.toLowerCase().includes(searchTerm));
         });

         if (filteredData.length > 0) {
             filteredData.forEach(jadwal => {
                 jadwalTbody.innerHTML += ` 
                     <tr> 
                         <td>${jadwal.nama_matkul}</td> 
                         <td>${jadwal.nama_dosen}</td> 
                         <td>${jadwal.nama_kelas}</td> 
                         <td>${jadwal.hari}</td> 
                         <td>${(jadwal.jam_mulai || '').substring(0, 5)} - ${(jadwal.jam_selesai || '').substring(0, 5)}</td> 
                         <td>${jadwal.ruangan}</td> 
                         <td> 
                             <button class="crud-button edit" data-id="${jadwal.id_jadwal}">Edit</button> 
                             <button class="crud-button delete" data-id="${jadwal.id_jadwal}">Hapus</button> 
                         </td> 
                     </tr>`; 
             }); 
         } else {
             jadwalTbody.innerHTML = '<tr><td colspan="7">Tidak ada data jadwal yang cocok.</td></tr>';
         }
     }

 
     function hideJadwalModal() {
         modal.classList.remove('active');
         modalContent.classList.remove('active');
         setTimeout(() => { modal.style.display = 'none'; }, 300);
     }
 
     async function showJadwalModal(title, id = null) {
         jadwalForm.reset();
         modalTitle.textContent = title;
         jadwalIdInput.value = id;
 
         try {
             const response = await fetch(`${API_URL}?action=get_form_data`);
             const result = await response.json();
             if (result.success) {
                 const { dosen, matakuliah, kelas } = result.data;
                 const dosenSelect = document.getElementById('jdwl-dosen');
                 const matkulSelect = document.getElementById('jdwl-matkul');
                 const kelasSelect = document.getElementById('jdwl-kelas');
                 
                 dosenSelect.innerHTML = '<option value="">Pilih Dosen</option>' + dosen.map(d => `<option value="${d.nidn}">${d.nama_lengkap}</option>`).join('');
                 matkulSelect.innerHTML = '<option value="">Pilih Mata Kuliah</option>' + matakuliah.map(mk => `<option value="${mk.id_matkul}">${mk.nama_matkul}</option>`).join('');
                 kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>' + kelas.map(k => `<option value="${k.id_kelas}">${k.nama_kelas}</option>`).join('');
                 
                 modal.style.display = 'flex';
                 setTimeout(() => {
                     modal.classList.add('active');
                     modalContent.classList.add('active');
                 }, 10);
             } else {
                 alert('Gagal memuat data untuk form.');
             }
         } catch (error) {
             console.error('Error fetching form data:', error);
             alert('Terjadi kesalahan saat memuat data form.');
         }
     }
     
     // Function to handle saving (add/edit) jadwal
     async function saveJadwal() {
     const id = document.getElementById('jadwal-id').value;
     const action = id ? 'update' : 'create';

     // Buat satu objek payload
     const payload = {
         action: action,
         id_jadwal: id,
         // Ambil nilai dari semua elemen form
         nidn_dosen: document.getElementById('jdwl-dosen').value,
         id_matkul: document.getElementById('jdwl-matkul').value,
         id_kelas: document.getElementById('jdwl-kelas').value,
         hari: document.getElementById('jadwal-hari').value,
         jam_mulai: document.getElementById('jadwal-jam-mulai').value,
         jam_selesai: document.getElementById('jadwal-jam-selesai').value,
         ruangan: document.getElementById('jadwal-ruangan').value
     };

         try {
             const response = await fetch(API_URL, {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json'
                 },
                 body: JSON.stringify(payload)
             });
             const result = await response.json();

             if (result.success) {
                 alert(result.message);
                 hideJadwalModal();
                 window.loadJadwalData();
             } else {
                 alert('Error: ' + result.message);
            }
         } catch (error) {
             console.error('Error:', error);
             alert('Terjadi kesalahan saat menyimpan jadwal: ' + error.message);
         }
     }

     // Function to handle editing jadwal (now just calls showJadwalModal with ID)
     async function editJadwal(id) {
         await showJadwalModal('Edit Jadwal', id);
         try {
             // PERBAIKAN: Gunakan action=get_by_id
             const response = await fetch(`${API_URL}?action=get_by_id&id=${id}`);
             const result = await response.json();

             if (result.success && result.data) {
                 const jadwalData = result.data;
                 document.getElementById('jdwl-dosen').value = jadwalData.id_dosen;
                 document.getElementById('jdwl-matkul').value = jadwalData.id_matkul;
                 document.getElementById('jdwl-kelas').value = jadwalData.id_kelas;
                 document.getElementById('jadwal-hari').value = jadwalData.hari;
                 document.getElementById('jadwal-jam-mulai').value = jadwalData.jam_mulai;
                 document.getElementById('jadwal-jam-selesai').value = jadwalData.jam_selesai;
                 document.getElementById('jadwal-ruangan').value = jadwalData.ruangan;
             } else {
                 alert('Error fetching single jadwal data: ' + result.message);
             }
         } catch (error) {
             console.error('Error fetching single jadwal data:', error);
             alert('An error occurred while fetching single jadwal data.');
         }
     }

     // Function to delete jadwal
     async function deleteJadwal(id) {
         if (confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
             try {
                 const response = await fetch(API_URL, {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json'
                     },
                     body: JSON.stringify({ action: 'delete', id_jadwal: id })
                 });
                 const result = await response.json();

                 if (result.success) {
                     alert('Jadwal berhasil dihapus!');
                     window.loadJadwalData(); // Reload data after deletion
                 } else {
                     alert('Error deleting jadwal: ' + result.message);
                 }
             } catch (error) {
                 console.error('Error:', error);
                 alert('An error occurred while deleting jadwal.');
             }
         }
     }
 
     // === EVENT LISTENERS === 
     document.getElementById('add-jadwal-btn').addEventListener('click', () => showJadwalModal('Tambah Jadwal Baru'));
     document.getElementById('cancel-jadwal').addEventListener('click', hideJadwalModal);
     document.getElementById('close-jadwal-modal').addEventListener('click', hideJadwalModal);
     
     // PERBAIKAN: Gunakan Event Delegation 
     jadwalTbody.addEventListener('click', function(e) {
         const target = e.target;
         const id = target.dataset.id;
 
         if (target.classList.contains('edit') && id) {
             editJadwal(id);
         }
         if (target.classList.contains('delete') && id) {
             deleteJadwal(id);
         }
     }); 
 
     document.getElementById('save-jadwal').addEventListener('click', saveJadwal);

     // Event listener untuk search input
     const jadwalSearchInput = document.getElementById('jadwal-search-input');
     if (jadwalSearchInput) {
         jadwalSearchInput.addEventListener('keyup', filterJadwalTable);
     }

     // Tambahkan event listener untuk modal overlay
     const modalOverlay = document.getElementById('jadwal-modal');
     if (modalOverlay) {
         modalOverlay.addEventListener('click', function(event) {
             // Jika yang diklik adalah overlay itu sendiri (bukan modal-content di dalamnya)
             if (event.target === modalOverlay) {
                 hideJadwalModal();
             }
         });
     }

     // Tandai bahwa event listener telah terpasang
     jadwalContent.dataset.listenersAttached = 'true';
     window.loadJadwalData(); 
 };