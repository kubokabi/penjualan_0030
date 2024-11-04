<?php
require_once 'app/views/Layouts/header.php';
session_start();
?>
<style>
    /* Style untuk main container */
    main.main {
        padding: 20px;
        border-radius: 8px;
    }

    .main-title {
        font-size: 2em;
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
        text-align: left;
        /* Rata kiri */
    }

    /* Style untuk form */
    .form-container {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 15px;
        text-align: left;
        /* Rata kiri */
    }

    .form-group label {
        font-weight: bold;
        color: #333;
    }

    .form-group input {
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ddd;
        margin-top: 5px;
        box-sizing: border-box;
    }

    .btn-primary {
        background-color: #5cb85c;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        transition: background-color 0.3s;
        width: 100%;
        font-size: 16px;
        cursor: pointer;
        text-align: center;
    }

    .btn-primary:hover {
        background-color: #4cae4c;
    }

    .btn-back {
        display: inline-block;
        margin-top: 10px;
        color: #007bff;
        text-decoration: none;
    }

    .btn-back:hover {
        text-decoration: underline;
    }
</style>
<?php if (isset($_SESSION['flash_message'])): ?>
    <script>
        console.log("Running SweetAlert script"); // Debugging log
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '<?= $_SESSION['flash_message']['type']; ?>',
                title: '<?= ucfirst($_SESSION['flash_message']['type']); ?>',
                text: '<?= $_SESSION['flash_message']['message']; ?>'
            });
        });
    </script>
    <?php unset($_SESSION['flash_message']); // Hapus pesan setelah ditampilkan 
    ?>
<?php endif; ?>



<main class="main users chart-page" id="skip-target">
    <div class="container">
        <h2 class="main-title">Tambah Barang</h2>
        <div class="form">
            <form action="index.php?action=storeBarang" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="kode_barang">Kode Barang</label>
                    <input type="text" id="kode_barang" name="kode_barang" placeholder="Masukan Kode Barang" required>
                </div>
                <div class="form-group">
                    <label for="nama_barang">Nama Barang</label>
                    <input type="text" id="nama_barang" name="nama_barang" placeholder="Masukan Nama Barang" required>
                </div>
                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input type="number" id="harga" name="harga" placeholder="Masukan Harga" required>
                </div>
                <div class="form-group">
                    <label for="stok">Stok</label>
                    <input type="number" id="stok" name="stok" placeholder="Masukan Stok" required>
                </div>
                <button type="submit" class="btn-primary">Simpan Barang</button>
            </form>
            <a href="index.php?action=barang" class="btn-back">Kembali ke Daftar Barang</a>
        </div>
    </div>
</main>

<?php
require_once 'app/views/Layouts/footer.php';
?>