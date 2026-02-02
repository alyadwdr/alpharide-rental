<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get all transactions or single transaction
        if (isset($_GET['id'])) {
            $id = clean_input($_GET['id']);
            $query = "SELECT t.*, u.nama, u.email, m.merek, m.model 
                      FROM transaksi t 
                      JOIN user u ON t.id_user = u.id_user 
                      JOIN mobil m ON t.id_mobil = m.id_mobil 
                      WHERE t.id_sewa = '$id'";
            $result = mysqli_query($conn, $query);
            $data = mysqli_fetch_assoc($result);
            echo json_encode($data);
        } else {
            $query = "SELECT t.*, u.nama, u.email, m.merek, m.model 
                      FROM transaksi t 
                      JOIN user u ON t.id_user = u.id_user 
                      JOIN mobil m ON t.id_mobil = m.id_mobil";
            
            // Filter by user
            if (isset($_GET['user_id'])) {
                $user_id = clean_input($_GET['user_id']);
                $query .= " WHERE t.id_user = '$user_id'";
            }
            
            // Filter by status
            if (isset($_GET['status'])) {
                $status = clean_input($_GET['status']);
                $query .= (strpos($query, 'WHERE') !== false ? " AND" : " WHERE") . " t.status = '$status'";
            }
            
            $query .= " ORDER BY t.created_at DESC";
            
            $result = mysqli_query($conn, $query);
            $data = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            echo json_encode($data);
        }
        break;
        
    case 'POST':
        // Create new transaction
        $input = json_decode(file_get_contents('php://input'), true);
        
        $id_mobil = clean_input($input['id_mobil']);
        $id_user = clean_input($input['id_user']);
        $tanggal_sewa = clean_input($input['tanggal_sewa']);
        $tanggal_kembali = clean_input($input['tanggal_kembali']);
        $total_harga = clean_input($input['total_harga']);
        
        $query = "INSERT INTO transaksi (id_mobil, id_user, tanggal_sewa, tanggal_kembali, total_harga, status) 
                  VALUES ('$id_mobil', '$id_user', '$tanggal_sewa', '$tanggal_kembali', '$total_harga', 'pending')";
        
        if (mysqli_query($conn, $query)) {
            // Update car status
            mysqli_query($conn, "UPDATE mobil SET status = 'disewa' WHERE id_mobil = '$id_mobil'");
            echo json_encode(['success' => true, 'message' => 'Transaksi berhasil dibuat', 'id' => mysqli_insert_id($conn)]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal membuat transaksi']);
        }
        break;
        
    case 'PUT':
        // Update transaction status
        $input = json_decode(file_get_contents('php://input'), true);
        
        $id_sewa = clean_input($input['id_sewa']);
        $status = clean_input($input['status']);
        
        $query = "UPDATE transaksi SET status = '$status' WHERE id_sewa = '$id_sewa'";
        
        if (mysqli_query($conn, $query)) {
            // Update car status based on transaction status
            if ($status == 'disetujui') {
                $id_mobil = clean_input($input['id_mobil']);
                mysqli_query($conn, "UPDATE mobil SET status = 'disewa' WHERE id_mobil = '$id_mobil'");
            } else if ($status == 'ditolak' || $status == 'selesai') {
                $id_mobil = clean_input($input['id_mobil']);
                mysqli_query($conn, "UPDATE mobil SET status = 'tersedia' WHERE id_mobil = '$id_mobil'");
            }
            
            echo json_encode(['success' => true, 'message' => 'Status transaksi berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengupdate status transaksi']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}
?>