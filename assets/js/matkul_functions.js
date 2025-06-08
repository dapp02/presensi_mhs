window.initMatkul = () => {
    window.loadMatkulData();
};

window.loadMatkulData = async () => {
    try {
        const response = await fetch('../App/Api/matakuliah_api.php');
        const result = await response.json();

        if (result.success) {
            const tableBody = document.querySelector('#matakuliah-crud tbody');
            tableBody.innerHTML = ''; // Clear existing data

            result.data.forEach(matkul => {
                const row = `
                    <tr>
                        <td>${matkul.id_matkul}</td>
                        <td>${matkul.kode_matkul}</td>
                        <td>${matkul.nama_matkul}</td>
                        <td>${matkul.sks}</td>
                        <td>${matkul.nama_prodi}</td>
                        <td>
                            <button class="btn btn-sm btn-warning edit-matkul-btn" data-id="${matkul.id_matkul}">Edit</button>
                            <button class="btn btn-sm btn-danger delete-matkul-btn" data-id="${matkul.id_matkul}">Delete</button>
                        </td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        } else {
            console.error('Failed to load Mata Kuliah data:', result.message);
            alert('Gagal memuat data Mata Kuliah: ' + result.message);
        }
    } catch (error) {
        console.error('Error loading Mata Kuliah data:', error);
        alert('Terjadi kesalahan saat memuat data Mata Kuliah.');
    }
};