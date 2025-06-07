document.addEventListener('DOMContentLoaded', function() {
    // === DEKLARASI VARIABEL & ELEMEN DOM ===
    const NIM_MAHASISWA_LOGIN = document.body.dataset.nimMahasiswa;
    let ID_JADWAL_AKTIF_UNTUK_ABSENSI = null;

    const hariItemsContainer = document.querySelector('.hari-container');
    const infoKelasContainer = document.getElementById('info-kelas-container');
    const absenSubtitle = document.querySelector('.absen-container .absen-subtitle');
    const statusTextElement = document.getElementById('statusText');
    const beforeAttendanceDiv = document.getElementById('beforeAttendance');
    const afterAttendanceDiv = document.getElementById('afterAttendance');
    
    // PERBAIKAN: Gunakan ID untuk selektor tombol agar lebih spesifik
    const btnHadir = document.getElementById('btn-absen-hadir');
    const btnIzin = document.getElementById('btn-absen-izin');
    const btnSakit = document.getElementById('btn-absen-sakit');

    // Validasi elemen penting
    if (!hariItemsContainer || !infoKelasContainer || !absenSubtitle || !statusTextElement) {
        console.error('JS ERROR: Satu atau lebih elemen UI penting tidak ditemukan.');
        return;
    }

    // === FUNGSI-FUNGSI HELPER ===
    function setAbsenButtonsState(disabled) {
        const absenButtons = [btnHadir, btnIzin, btnSakit];
        absenButtons.forEach(btn => {
            if(btn) {
                btn.disabled = disabled; // Gunakan atribut 'disabled' untuk tombol
                btn.style.pointerEvents = disabled ? 'none' : 'auto';
                btn.style.opacity = disabled ? '0.5' : '1';
            }
        });
        if (beforeAttendanceDiv) {
            beforeAttendanceDiv.style.display = disabled ? 'none' : 'block';
        }
    }

    function updateUserVisualAktifHari(clickedItemElement) {
        if (!clickedItemElement) return;
        hariItemsContainer.querySelectorAll('.day-item').forEach(i => {
            i.classList.remove('active-day');
            const line = i.querySelector('.hari-text-line');
            if (line) line.style.display = 'none';
        });

        clickedItemElement.classList.add('active-day');
        let activeLine = clickedItemElement.querySelector('.hari-text-line');
        if (activeLine) activeLine.style.display = 'block';
    }

    // PERBAIKAN: Definisi fungsi dipindahkan ke scope atas
    function updateAbsensiPanel(jadwal) {
        if (jadwal && jadwal.status_kehadiran) {
            if (beforeAttendanceDiv) beforeAttendanceDiv.style.display = 'none';
            if (afterAttendanceDiv) afterAttendanceDiv.style.display = 'block';
            statusTextElement.textContent = 'Status kamu: ' + jadwal.status_kehadiran;
            setAbsenButtonsState(true); // Removed second argument
            
            // Update rekap
            document.getElementById('absenCount').textContent = (jadwal.status_kehadiran === 'Hadir') ? '1' : '0';
            document.getElementById('izinCount').textContent = (jadwal.status_kehadiran === 'Izin') ? '1' : '0';
            document.getElementById('sakitCount').textContent = (jadwal.status_kehadiran === 'Sakit') ? '1' : '0';
            document.getElementById('alphaCount').textContent = (jadwal.status_kehadiran === 'Alpha') ? '1' : '0';
        } else {
            if (beforeAttendanceDiv) beforeAttendanceDiv.style.display = 'block';
            if (afterAttendanceDiv) afterAttendanceDiv.style.display = 'none';
            // Status teks dan tombol akan diatur oleh logika di fetchJadwal...
        }
    }

    // --- FUNGSI INTI: FETCH & UPDATE JADWAL ---
    async function fetchJadwalMahasiswaUntukHari(nim, namaHari, tanggalIso) {
        if (!nim || !namaHari || !tanggalIso) {
            console.warn(`JS WARN: Panggilan fetchJadwal dibatalkan, data tidak valid.`);
            return;
        }

        infoKelasContainer.innerHTML = '<p class="info-title">Informasi Kelas Hari Ini :</p><p style="text-align:center; margin-top:20px;">Memuat jadwal...</p>';
        absenSubtitle.textContent = 'Memuat...';
        setAbsenButtonsState(true);

        try {
            const apiUrl = `../App/Api/get_jadwal_mahasiswa_by_hari.php?nim=${encodeURIComponent(nim)}&nama_hari=${encodeURIComponent(namaHari)}&tanggal=${encodeURIComponent(tanggalIso)}`;
            const response = await fetch(apiUrl);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const result = await response.json();
            
            infoKelasContainer.innerHTML = '<p class="info-title">Informasi Kelas Hari Ini :</p>';



            if (result.success && result.data && result.data.length > 0) {
                result.data.forEach((jadwal, index) => {
                    const jadwalWrapperDiv = document.createElement('div');
                    jadwalWrapperDiv.className = 'info-grid';
                    jadwalWrapperDiv.dataset.idJadwal = jadwal.id_jadwal;
                    jadwalWrapperDiv.dataset.namaMatkul = jadwal.nama_matkul;
                    // Simpan seluruh objek jadwal sebagai string JSON untuk diambil nanti saat klik
                    jadwalWrapperDiv.dataset.jadwalJson = JSON.stringify(jadwal);
                    
                    jadwalWrapperDiv.innerHTML = `
                        <div class="info-item"><img src="../assets/images/teachings.png" alt="Mata Kuliah" class="info-icon"><span>${jadwal.nama_matkul || 'N/A'}</span></div>
                        <div class="info-item"><img src="../assets/images/clock.png" alt="Jam" class="info-icon"><span>${(jadwal.jam_mulai || 'N/A').substring(0, 5)} - ${(jadwal.jam_selesai || 'N/A').substring(0, 5)}</span></div>
                        <div class="info-item"><img src="../assets/images/classroom.png" alt="Ruangan" class="info-icon"><span>${jadwal.ruangan || 'N/A'}</span></div>
                        <div class="info-item"><img src="../assets/images/conference.png" alt="Dosen" class="info-icon"> 
                            <span>${jadwal.nama_dosen || 'N/A'}</span> </div>
                    `;
                    infoKelasContainer.appendChild(jadwalWrapperDiv);

                    // Add a horizontal rule if it's not the last item
                    if (index < result.data.length - 1) {
                        const hr = document.createElement('hr');
                        infoKelasContainer.appendChild(hr);
                    }
                });

                if (result.data.length === 1) {
                    const jadwal = result.data[0];
                    updateAbsensiPanel(jadwal); // Gunakan helper untuk update panel absen
                    absenSubtitle.textContent = jadwal.nama_matkul;
                    ID_JADWAL_AKTIF_UNTUK_ABSENSI = jadwal.id_jadwal;
                    // Aktifkan atau nonaktifkan tombol berdasarkan status kehadiran
                    setAbsenButtonsState(!!jadwal.status_kehadiran);
                    if (!jadwal.status_kehadiran) statusTextElement.textContent = 'Kamu Belum Absen';
                    infoKelasContainer.querySelector('.info-grid').classList.add('jadwal-aktif');
                } else {
                    absenSubtitle.textContent = 'Pilih Jadwal';
                    ID_JADWAL_AKTIF_UNTUK_ABSENSI = null;
                    statusTextElement.textContent = 'Silakan pilih jadwal untuk absen';
                    setAbsenButtonsState(true);
                    updateAbsensiPanel(null); // Reset ke tampilan tombol
                }
            } else {
                ID_JADWAL_AKTIF_UNTUK_ABSENSI = null;
                absenSubtitle.textContent = 'Tidak ada jadwal';
                statusTextElement.textContent = 'Tidak ada jadwal untuk diabsen';
                setAbsenButtonsState(true);
                infoKelasContainer.innerHTML += `
                    <img src="../assets/images/browser.png" alt="Tidak ada kelas" style="width:100px; margin:20px auto; display:block;">
                    <p style="text-align:center; font-size:1.2em; margin-top:10px;">${result.message || 'Tidak ada kelas hari ini.'}</p>
                `;
            }
        } catch (error) {
            console.error('JS ERROR: Gagal fetch atau proses jadwal:', error);
            infoKelasContainer.innerHTML = `<p class="info-title">Informasi Kelas Hari Ini :</p><p style="text-align:center; margin-top:20px; color:red;">Gagal memuat jadwal. ${error.message}</p>`;
            absenSubtitle.textContent = 'Error';
            setAbsenButtonsState(true);
        }
    }

    // --- FUNGSI UNTUK SUBMIT ABSENSI ---
    async function submitUserAbsensi(statusKehadiran) {
        if (!NIM_MAHASISWA_LOGIN || !ID_JADWAL_AKTIF_UNTUK_ABSENSI || !statusKehadiran) {
            alert('Informasi tidak lengkap untuk mengirim absensi (NIM, Jadwal, atau Status). Silakan pilih jadwal terlebih dahulu.');
            console.error("JS ERROR: Data tidak lengkap untuk absensi.", { nim: NIM_MAHASISWA_LOGIN, idJadwal: ID_JADWAL_AKTIF_UNTUK_ABSENSI, status: statusKehadiran });
            return;
        }

        const absenData = {
            nim: NIM_MAHASISWA_LOGIN,
            id_jadwal: ID_JADWAL_AKTIF_UNTUK_ABSENSI,
            status_kehadiran: statusKehadiran
        };

        try {
            const response = await fetch('../App/Api/submit_absensi_mahasiswa.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(absenData)
            });

            const result = await response.json();

            if (result.success) {
                alert('Absensi berhasil: ' + result.message);
                // Use the new helper function to update the UI
                // After successful submission, we need to re-fetch the schedule to get the updated status
                // Or, we can manually update the UI based on the submitted status
                // For now, let's assume we update the UI manually for the single active schedule
                const currentJadwal = { status_kehadiran: statusKehadiran }; // Create a dummy object for updateAbsensiPanel
                updateAbsensiPanel(currentJadwal);
                setAbsenButtonsState(true); // Disable buttons after successful submission
            } else {
                alert('Gagal mengajukan absensi: ' + result.message);
            }
        } catch (error) {
            console.error('JS ERROR: Gagal mengirim absensi:', error);
            alert('Terjadi kesalahan saat mengirim absensi: ' + error.message);
        }
    }

    // Make submitUserAbsensi globally accessible for button event listeners
    window.submitUserAbsensi = submitUserAbsensi;

    // --- EVENT LISTENERS (Struktur direvisi untuk kejelasan) ---
    function setupEventListeners() {
        if (hariItemsContainer) {
            hariItemsContainer.addEventListener('click', function(event) {
                const clickedItem = event.target.closest('.day-item');
                if (!clickedItem) return;
                setAbsenButtonsState(true); // Disable buttons immediately
                updateUserVisualAktifHari(clickedItem);
                const namaHariDipilih = clickedItem.dataset.hari;
                const tanggalIsoDipilih = clickedItem.dataset.tanggalIso; // Ambil tanggal dari atribut
                fetchJadwalMahasiswaUntukHari(NIM_MAHASISWA_LOGIN, namaHariDipilih, tanggalIsoDipilih); // Teruskan ke fungsi
            });
        }

        if (infoKelasContainer) {
            infoKelasContainer.addEventListener('click', function(event) {
                const clickedJadwalElement = event.target.closest('.info-grid');
                if (!clickedJadwalElement) return;

                infoKelasContainer.querySelectorAll('.info-grid').forEach(item => item.classList.remove('jadwal-aktif'));
                clickedJadwalElement.classList.add('jadwal-aktif');

                const namaMatkulDipilih = clickedJadwalElement.dataset.namaMatkul;
                const idJadwalDipilih = clickedJadwalElement.dataset.idJadwal;
                const jadwalData = JSON.parse(clickedJadwalElement.dataset.jadwalJson); // Parse the stored JSON

                if (namaMatkulDipilih && idJadwalDipilih) {
                    absenSubtitle.textContent = namaMatkulDipilih;
                    ID_JADWAL_AKTIF_UNTUK_ABSENSI = idJadwalDipilih;
                    updateAbsensiPanel(jadwalData); // Update panel based on clicked jadwal's data
                    setAbsenButtonsState(!!jadwalData.status_kehadiran, "Jadwal dipilih, status: " + (jadwalData.status_kehadiran || 'Belum Absen'));
                    if (!jadwalData.status_kehadiran) statusTextElement.textContent = 'Kamu Belum Absen';
                }
            });
        }
        
        // Gunakan ID untuk menambahkan event listener tombol absen
        if (btnHadir) btnHadir.addEventListener('click', () => submitUserAbsensi('Hadir'));
        if (btnIzin) btnIzin.addEventListener('click', () => submitUserAbsensi('Izin'));
        if (btnSakit) btnSakit.addEventListener('click', () => submitUserAbsensi('Sakit'));
    }

    // --- INISIALISASI ---
    function initializeDashboard() {
        setAbsenButtonsState(true); // Nonaktifkan tombol di awal
        setupEventListeners(); // Pasang semua event listener

        const today = new Date();
        const todayIso = today.toISOString().split('T')[0];
        let initialDayItem = hariItemsContainer.querySelector(`.day-item[data-tanggal-iso="${todayIso}"]`);

        if (!initialDayItem) {
            console.warn(`JS WARN: Hari ini (${todayIso}) tidak ditemukan di kalender. Fallback ke hari pertama.`);
            initialDayItem = hariItemsContainer.querySelector('.day-item');
        }

        if (initialDayItem) {
            updateUserVisualAktifHari(initialDayItem);
            const initialHariAktif = initialDayItem.dataset.hari;
            const initialTanggalIso = initialDayItem.dataset.tanggalIso; // Ambil juga tanggal ISO awal
            fetchJadwalMahasiswaUntukHari(NIM_MAHASISWA_LOGIN, initialHariAktif, initialTanggalIso); // Teruskan ke fungsi
        } else {
            console.error('JS ERROR: Tidak ada item hari di kalender untuk dimuat.');
            absenSubtitle.textContent = 'Tidak ada data kalender';
        }
    }

    initializeDashboard(); // Panggil fungsi utama

    // === AWAL BLOK BARU: LOGIKA SEARCH BAR PANEL KANAN ===
    const searchInput = document.getElementById('matkul-search-input');
    const kelasContainer = document.getElementById('kelas-list-container');

    if (searchInput && kelasContainer) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const semuaKartu = kelasContainer.querySelectorAll('.kelas-card');
            let visibleCardsCount = 0;

            semuaKartu.forEach(card => {
                // Ambil teks dari nama mata kuliah dan nama dosen
                const namaMatkulElement = card.querySelector('.kelas-header');
                const namaDosenElement = card.querySelector('.kelas-dosen span');

                const namaMatkul = namaMatkulElement ? namaMatkulElement.textContent.toLowerCase() : '';
                const namaDosen = namaDosenElement ? namaDosenElement.textContent.toLowerCase() : '';

                // Cek apakah searchTerm cocok dengan salah satu dari keduanya
                if (namaMatkul.includes(searchTerm) || namaDosen.includes(searchTerm)) {
                    card.style.display = 'block'; // atau '' untuk kembali ke default display
                    visibleCardsCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Tampilkan atau sembunyikan pesan "tidak ada hasil"
            const noResultsMessage = document.getElementById('search-no-results-user');
            if (noResultsMessage) {
                if (visibleCardsCount === 0 && searchTerm !== '') {
                    noResultsMessage.style.display = 'block';
                } else {
                    noResultsMessage.style.display = 'none';
                }
            }
        });
    }
    // === AKHIR BLOK BARU ===

});