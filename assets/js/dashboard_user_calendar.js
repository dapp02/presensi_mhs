document.addEventListener('DOMContentLoaded', function() {
    const bodyElement = document.body;
    const NIM_MAHASISWA_LOGIN = bodyElement.dataset.nimMahasiswa;

    const hariItemsContainer = document.querySelector('.hari-container');
    const infoKelasContainer = document.getElementById('info-kelas-container');
    const absenSubtitle = document.querySelector('.absen-container .absen-subtitle');

    const absenButtons = document.querySelectorAll('.absen-container .absen-btn');
    const beforeAttendanceDiv = document.getElementById('beforeAttendance');

    function setAbsenButtonsState(disabled) {
        absenButtons.forEach(btn => btn.style.pointerEvents = disabled ? 'none' : 'auto');
        absenButtons.forEach(btn => btn.style.opacity = disabled ? '0.5' : '1'); // Add opacity change
        if (beforeAttendanceDiv) beforeAttendanceDiv.style.opacity = disabled ? '0.5' : '1';
    }

    // Panggil saat halaman dimuat
    setAbsenButtonsState(true);
    if(document.getElementById('statusText')) document.getElementById('statusText').textContent = 'Pilih jadwal untuk absen';

    // LOGGING AWAL UNTUK VERIFIKASI
    console.log("JS LOG (User Initial): NIM_MAHASISWA_LOGIN dari DOM:", NIM_MAHASISWA_LOGIN);
    if (!NIM_MAHASISWA_LOGIN) {
        console.error('JS FATAL (User): NIM Mahasiswa TIDAK DITEMUKAN di atribut data DOM. Periksa pages/dashboard_user.php.');
    }

    if (!hariItemsContainer || !infoKelasContainer || !absenSubtitle) {
        console.error('JS ERROR (User): Elemen UI penting (hari-container, info-kelas-container, atau absen-subtitle) tidak ditemukan.');
        return;
    }

    function updateUserVisualAktifHari(clickedItemElement) {
        hariItemsContainer.querySelectorAll('.day-item').forEach(i => {
            i.classList.remove('active-day');
            const line = i.querySelector('.hari-text-line');
            if (line) line.style.display = 'none';
        });
        clickedItemElement.classList.add('active-day');
        let activeLine = clickedItemElement.querySelector('.hari-text-line');
        if (!activeLine) {
            activeLine = document.createElement('div');
            activeLine.className = 'hari-text-line';
            const tanggalSpan = clickedItemElement.querySelector('.tanggal');
            if (tanggalSpan) clickedItemElement.insertBefore(activeLine, tanggalSpan.nextSibling);
            else clickedItemElement.appendChild(activeLine);
        }
        activeLine.style.display = 'block';
    }

    let ID_JADWAL_AKTIF_UNTUK_ABSENSI = null; // Global variable to store the active jadwal ID

    async function fetchJadwalMahasiswaUntukHari(nim, namaHari) {
        if (!nim || !namaHari) {
            console.warn(`JS WARN (User): Panggilan fetchJadwal dibatalkan, NIM (${nim}) atau NamaHari (${namaHari}) tidak valid.`);
            infoKelasContainer.innerHTML = '<p class="info-title">Informasi Kelas Hari Ini :</p><p style="text-align:center; margin-top:20px; color:orange;">Data tidak lengkap untuk memuat jadwal.</p>';
            absenSubtitle.textContent = 'Data tidak lengkap';
            return;
        }

        console.log(`JS LOG (User): Memulai fetch jadwal untuk NIM=${nim}, Hari=${namaHari}`);
        const apiUrl = `../App/Api/get_jadwal_mahasiswa_by_hari.php?nim=${encodeURIComponent(nim)}&nama_hari=${encodeURIComponent(namaHari)}`;

        infoKelasContainer.innerHTML = '<p class="info-title">Informasi Kelas Hari Ini :</p><p style="text-align:center; margin-top:20px;">Memuat jadwal untuk hari ' + namaHari + '...</p>';
        absenSubtitle.textContent = 'Memuat...';
        setAbsenButtonsState(true); // Disable buttons while loading

        try {
            const response = await fetch(apiUrl);
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Gagal mengambil data. Status: ${response.status}. Pesan Server: ${errorText || response.statusText}`);
            }
            const result = await response.json();
            console.log('JS LOG (User): Respons API diterima:', result);

            infoKelasContainer.innerHTML = '<p class="info-title">Informasi Kelas Hari Ini :</p>';

            if (result.success && result.data && result.data.length > 0) {
                result.data.forEach((jadwal, index) => {
                    const jamMulai = jadwal.jam_mulai ? jadwal.jam_mulai.substring(0, 5) : 'N/A';
                    const jamSelesai = jadwal.jam_selesai ? jadwal.jam_selesai.substring(0, 5) : 'N/A';

                    const jadwalWrapperDiv = document.createElement('div');
                    jadwalWrapperDiv.className = 'info-grid';
                    jadwalWrapperDiv.innerHTML = `
                        <div class="info-item">
                            <img src="../assets/images/teachings.png" alt="Mata Kuliah" class="info-icon">
                            <span>${jadwal.nama_matkul || 'N/A'}</span>
                        </div>
                        <div class="info-item">
                            <img src="../assets/images/clock.png" alt="Jam" class="info-icon">
                            <span>${jamMulai} - ${jamSelesai}</span>
                        </div>
                        <div class="info-item">
                            <img src="../assets/images/classroom.png" alt="Ruangan" class="info-icon">
                            <span>${jadwal.ruangan || 'N/A'}</span>
                        </div>
                        <div class="info-item">
                            <img src="../assets/images/conference.png" alt="Dosen" class="info-icon">
                            <span>${htmlspecialchars(jadwal.nama_dosen || 'N/A')}</span>
                        </div>
                    `;
                    infoKelasContainer.appendChild(jadwalWrapperDiv);
                    if (index < result.data.length - 1) {
                        const hr = document.createElement('hr');
                        infoKelasContainer.appendChild(hr);
                    }
                });
                absenSubtitle.textContent = result.data[0].nama_matkul || 'Pilih Mata Kuliah';
                ID_JADWAL_AKTIF_UNTUK_ABSENSI = result.data[0].id_jadwal || null;

                // --- AWAL BLOK PERBAIKAN ---
                // Reset tampilan ke kondisi "belum absen" setiap kali jadwal baru dimuat
                const statusTextElement = document.getElementById('statusText');
                const beforeAttendanceDiv = document.getElementById('beforeAttendance');
                const afterAttendanceDiv = document.getElementById('afterAttendance');

                if (statusTextElement) {
                    statusTextElement.textContent = 'Kamu Belum Absen'; // Perbaikan Poin 1
                }
                if (beforeAttendanceDiv) {
                    beforeAttendanceDiv.classList.remove('hidden');
                    beforeAttendanceDiv.style.display = 'block'; // Atau '' tergantung default Anda
                }
                if (afterAttendanceDiv) {
                    afterAttendanceDiv.classList.add('hidden');
                    afterAttendanceDiv.style.display = 'none'; // Perbaikan Poin 2
                }
                setAbsenButtonsState(false); // Aktifkan tombol (dari Sesi DU-4.1c)
            } else {
                ID_JADWAL_AKTIF_UNTUK_ABSENSI = null; // Reset if no schedule
                console.log("JS LOG (User FetchJadwal - DU-4.1a): Tidak ada jadwal, ID Jadwal aktif di-reset ke null.");
                const message = 'Tidak ada jadwal kuliah ditemukan untuk hari ini.';
                infoKelasContainer.innerHTML = `
                    <img src="../assets/images/browser.png" alt="Tidak ada kelas" style="width:100px; margin:20px auto; display:block;">
                    <p style="text-align:center; font-size:1.2em; margin-top:10px;">${message}</p>
                `;
                absenSubtitle.textContent = 'Tidak ada jadwal';
                ID_JADWAL_AKTIF_UNTUK_ABSENSI = null;

                // --- AWAL BLOK PERBAIKAN ---
                const statusTextElement = document.getElementById('statusText');
                const beforeAttendanceDiv = document.getElementById('beforeAttendance');
                const afterAttendanceDiv = document.getElementById('afterAttendance');

                if (statusTextElement) {
                    statusTextElement.textContent = 'Pilih jadwal untuk absen';
                }
                if (beforeAttendanceDiv) { // Tampilkan tombol tapi dalam keadaan disabled
                     beforeAttendanceDiv.classList.remove('hidden');
                     beforeAttendanceDiv.style.display = 'block';
                }
                if (afterAttendanceDiv) { // Sembunyikan rekap
                    afterAttendanceDiv.classList.add('hidden');
                    afterAttendanceDiv.style.display = 'none';
                }
                setAbsenButtonsState(true); // Nonaktifkan tombol (dari Sesi DU-4.1c)
                // --- AKHIR BLOK PERBAIKAN ---
            }
        } catch (error) {
            console.error('JS ERROR (User): Gagal fetch atau proses jadwal:', error);
            infoKelasContainer.innerHTML = '<p class="info-title">Informasi Kelas Hari Ini :</p>';
            infoKelasContainer.innerHTML += `<p style="text-align:center; margin-top:20px; color:red;">Gagal memuat jadwal. ${error.message}</p>`;
            absenSubtitle.textContent = 'Error';
            setAbsenButtonsState(true); // Nonaktifkan tombol jika ada error
        }
    }

    // Helper function for HTML escaping
    function htmlspecialchars(str) {
        if (typeof str !== 'string') return str;
        return str.replace(/&/g, '&amp;')
                   .replace(/</g, '&lt;')
                   .replace(/>/g, '&gt;')
                   .replace(/"/g, '&quot;')
                   .replace(/'/g, '&#039;');
    }

    // New function to submit attendance
    async function submitUserAbsensi(statusKehadiran) {
        // LOGGING SEBELUM VALIDASI DAN FETCH
        console.log("JS LOG (Absen - DU-4.1a): Fungsi submitUserAbsensi dipanggil dengan status:", statusKehadiran);
        console.log("JS LOG (Absen - DU-4.1a): Nilai NIM_MAHASISWA_LOGIN saat ini:", NIM_MAHASISWA_LOGIN);
        console.log("JS LOG (Absen - DU-4.1a): Nilai ID_JADWAL_AKTIF_UNTUK_ABSENSI saat ini:", ID_JADWAL_AKTIF_UNTUK_ABSENSI);

        if (!NIM_MAHASISWA_LOGIN || !ID_JADWAL_AKTIF_UNTUK_ABSENSI || !statusKehadiran) { // Tambahkan !statusKehadiran
            alert('Informasi tidak lengkap untuk mengirim absensi (NIM, Jadwal, atau Status). Silakan pilih jadwal terlebih dahulu.');
            console.error("JS ERROR (Absen - DU-4.1a): Data tidak lengkap.", {
                nim: NIM_MAHASISWA_LOGIN,
                idJadwal: ID_JADWAL_AKTIF_UNTUK_ABSENSI,
                status: statusKehadiran
            });
            return;
        }

        console.log(`JS LOG (User Absensi): Mengajukan absensi untuk NIM=${NIM_MAHASISWA_LOGIN}, ID_JADWAL=${ID_JADWAL_AKTIF_UNTUK_ABSENSI}, Status=${statusKehadiran}`);

        const absenData = {
            nim: NIM_MAHASISWA_LOGIN,
            id_jadwal: ID_JADWAL_AKTIF_UNTUK_ABSENSI,
            status_kehadiran: statusKehadiran
        };

        try {
            const response = await fetch('../App/Api/submit_absensi_mahasiswa.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(absenData)
            });

            const result = await response.json();
            console.log('JS LOG (User Absensi): Respons API absensi:', result);

            if (result.success) {
                alert('Absensi berhasil: ' + result.message);
                // Update UI to show 'afterAttendance' state
                document.getElementById('beforeAttendance').style.display = 'none';
                document.getElementById('afterAttendance').style.display = 'block';
                document.getElementById('absenCount').textContent = '1'; // Placeholder, ideally fetch actual count
                document.getElementById('izinCount').textContent = '0';
                document.getElementById('sakitCount').textContent = '0';

                // Update the specific count based on statusKehadiran
                if (statusKehadiran === 'Hadir') {
                    document.getElementById('absenCount').textContent = '1';
                } else if (statusKehadiran === 'Izin') {
                    document.getElementById('izinCount').textContent = '1';
                } else if (statusKehadiran === 'Sakit') {
                    document.getElementById('sakitCount').textContent = '1';
                }

            } else {
                alert('Gagal mengajukan absensi: ' + result.message);
            }
        } catch (error) {
            console.error('JS ERROR (User Absensi): Gagal mengirim absensi:', error);
            alert('Terjadi kesalahan saat mengirim absensi: ' + error.message);
        }
    }

    // Make submitUserAbsensi globally accessible
    window.submitUserAbsensi = submitUserAbsensi;

    // --- Event Listener untuk Klik Hari ---
    hariItemsContainer.addEventListener('click', function(event) {
        const clickedItem = event.target.closest('.day-item');
        if (!clickedItem) return;

        // Disable buttons immediately when a new day is clicked
        setAbsenButtonsState(true);

        updateUserVisualAktifHari(clickedItem);

        const namaHariDipilih = clickedItem.dataset.hari;
        const tanggalIsoDipilih = clickedItem.dataset.tanggalIso; // Not used in fetch, but good for logging

        fetchJadwalMahasiswaUntukHari(NIM_MAHASISWA_LOGIN, namaHariDipilih);
    });

    // Initial load for today's schedule
    const today = new Date();
    const todayDayItem = hariItemsContainer.querySelector(`.day-item[data-tanggal-iso="${today.toISOString().split('T')[0]}"]`);
    if (todayDayItem) {
        updateUserVisualAktifHari(todayDayItem);
        fetchJadwalMahasiswaUntukHari(NIM_MAHASISWA_LOGIN, todayDayItem.dataset.hari);
    }
});