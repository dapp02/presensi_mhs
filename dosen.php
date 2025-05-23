<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Mahasiswa</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            display: flex;
            flex-direction: column;
            background-color: #f5f5f5;
        }
        header {
            background-color: #2d8f2d;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }
        main {
            display: flex;
            padding: 20px;
        }
        .sidebar {
            width: 30%;
            padding: 20px;
        }
        .schedule, .attendance {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .class-info-container {
            width: 70%;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .class-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 20px;
        }
        .class-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Presensi Mahasiswa</div>
        <nav>
            <a href="#">Beranda</a>
            <a href="#">Keluar</a>
        </nav>
        <div class="user">Nama Dosen</div>
    </header>
    <main>
        <aside class="sidebar">
            <section class="schedule">
                <h2>Jadwal Minggu Ini</h2>
                <p>DD - MM - YYYY</p>
                <div class="class-info">
                    <p>Praktik Pemrograman Berbasis Web</p>
                    <p>07:00 - 14:25</p>
                    <p>Kelas 2B</p>
                </div>
            </section>
            <section class="attendance">
                <h2>Absen Mahasiswa</h2>
                <div class="attendance-icon">[Icon]</div>
            </section>
        </aside>
        <section class="class-info-container">
            <h2>Informasi Kelas</h2>
            <input type="text" placeholder="Cari berdasarkan nama kelas atau dosen">
            <div class="class-list">
                <div class="class-card">Mata Kuliah 1 - Kelas 2A</div>
                <div class="class-card">Mata Kuliah 2 - Kelas 2B</div>
                <div class="class-card">Mata Kuliah 3 - Kelas 2A</div>
                <div class="class-card">Mata Kuliah 4 - Kelas 2B</div>
                <div class="class-card">Mata Kuliah 5 - Kelas 2A</div>
                <div class="class-card">Mata Kuliah 6 - Kelas 2B</div>
            </div>
        </section>
    </main>
</body>
</html>
