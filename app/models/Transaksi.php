<?php

class Transaksi
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    public function getTransaksiByid_transaksi($id_transaksi)
    {
        $stmt = $this->db->prepare("SELECT * FROM transaksi WHERE id_transaksi = :id_transaksi");
        $stmt->bindParam(':id_transaksi', $id_transaksi, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mendapatkan semua transaksi
    public function getAllTransaksi()
    {
        $stmt = $this->db->prepare("SELECT * FROM transaksi ORDER BY id_transaksi");
        $stmt = $this->db->prepare("SELECT * FROM detail_transaksi ORDER BY id_detail");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function addTransaksi($data)
{
    try {
        $this->db->beginTransaction();

        // Validasi stok untuk setiap barang
        foreach ($data['kode_barang'] as $index => $kode_barang) {
            $jumlah = $data['jumlah'][$kode_barang];

            // Cek stok barang di tabel barang
            $checkStokStmt = $this->db->prepare("SELECT stok FROM barang WHERE kode_barang = :kode_barang");
            $checkStokStmt->bindParam(':kode_barang', $kode_barang, PDO::PARAM_STR);
            $checkStokStmt->execute();
            $stok = $checkStokStmt->fetchColumn();

            if ($stok < $jumlah) {
                // Jika stok tidak mencukupi, rollback transaksi dan kembalikan pesan error
                $this->db->rollBack();
                error_log("Stok tidak mencukupi untuk barang: $kode_barang");
                return false; // Anda bisa mengembalikan pesan khusus jika diperlukan
            }
        }

        // Simpan transaksi utama (tanggal akan otomatis terisi jika menggunakan TIMESTAMP DEFAULT CURRENT_TIMESTAMP)
        $stmt = $this->db->prepare("INSERT INTO transaksi (id_pelanggan, total_harga) VALUES (:id_pelanggan, :total_harga)");
        $stmt->bindParam(':id_pelanggan', $data['id_pelanggan'], PDO::PARAM_STR);
        $stmt->bindParam(':total_harga', $data['total_harga'], PDO::PARAM_INT);
        $stmt->execute();

        // Ambil ID transaksi yang baru saja dimasukkan
        $id_transaksi = $this->db->lastInsertId();

        // Simpan setiap barang di keranjang ke dalam tabel detail_transaksi
        foreach ($data['kode_barang'] as $index => $kode_barang) {
            $jumlah = $data['jumlah'][$kode_barang];
            $harga = $data['harga'][$index];

            // Insert ke tabel detail_transaksi
            $stmt = $this->db->prepare("INSERT INTO detail_transaksi (id_transaksi, kode_barang, jumlah, harga) VALUES (:id_transaksi, :kode_barang, :jumlah, :harga)");
            $stmt->bindParam(':id_transaksi', $id_transaksi, PDO::PARAM_INT);
            $stmt->bindParam(':kode_barang', $kode_barang, PDO::PARAM_STR);
            $stmt->bindParam(':jumlah', $jumlah, PDO::PARAM_INT);
            $stmt->bindParam(':harga', $harga, PDO::PARAM_INT);
            $stmt->execute();

            // Update stok barang di tabel barang
            $updateStokStmt = $this->db->prepare("UPDATE barang SET stok = stok - :jumlah WHERE kode_barang = :kode_barang");
            $updateStokStmt->bindParam(':jumlah', $jumlah, PDO::PARAM_INT);
            $updateStokStmt->bindParam(':kode_barang', $kode_barang, PDO::PARAM_STR);
            $updateStokStmt->execute();
        }

        // Commit transaksi jika semua berhasil
        $this->db->commit();
        return true;

    } catch (Exception $e) {
        // Rollback jika ada kesalahan
        $this->db->rollBack();
        error_log($e->getMessage());
        return false;
    }
}
  

}
