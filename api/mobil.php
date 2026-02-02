<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get all cars or single car
        if (isset($_GET['id'])) {
            $id = clean_input($_GET['id']);
            $query = "SELECT * FROM mobil WHERE id_mobil = '$id'";
            $result = mysqli_query($conn, $query);
            $data = mysqli_fetch_assoc($result);
            echo json_encode($data);
        } else {
            $query = "SELECT * FROM mobil";
            
            // Filter by status
            if (isset($_GET['status'])) {
                $status = clean_input($_GET['status']);
                $query .= " WHERE status = '$status'";
            }
            
            $result = mysqli_query($conn, $query);
            $data = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            echo json_encode($data);
        }
        break;
        
    case 'POST':
        // Add new car
        $input = json_decode(file_get_contents('php://input'), true);
        
        $merek = clean_input($input['merek']);
        $model = clean_input($input['model']);
        $warna = clean_input($input['warna']);
        $plat_nomor = clean_input($input['plat_nomor']);
        $kapasitas = clean_input($input['kapasitas']);
        $harga_per_hari = clean_input($input['harga_per_hari']);
        $foto = clean_input($input['foto']);
        $status = clean_input($input['status']);
        
        $query = "INSERT INTO mobil (merek, model, warna, plat_nomor, kapasitas, harga_per_hari, foto, status) 
                  VALUES ('$merek', '$model', '$warna', '$plat_nomor', '$kapasitas', '$harga_per_hari', '$foto', '$status')";
        
        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true, 'message' => 'Mobil berhasil ditambahkan', 'id' => mysqli_insert_id($conn)]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan mobil']);
        }
        break;
        
    case 'PUT':
        // Update car
        $input = json_decode(file_get_contents('php://input'), true);
        
        $id = clean_input($input['id_mobil']);
        $merek = clean_input($input['merek']);
        $model = clean_input($input['model']);
        $warna = clean_input($input['warna']);
        $plat_nomor = clean_input($input['plat_nomor']);
        $kapasitas = clean_input($input['kapasitas']);
        $harga_per_hari = clean_input($input['harga_per_hari']);
        $status = clean_input($input['status']);
        
        $query = "UPDATE mobil SET merek='$merek', model='$model', warna='$warna', 
                  plat_nomor='$plat_nomor', kapasitas='$kapasitas', harga_per_hari='$harga_per_hari', 
                  status='$status' WHERE id_mobil='$id'";
        
        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true, 'message' => 'Mobil berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengupdate mobil']);
        }
        break;
        
    case 'DELETE':
        // Delete car
        if (isset($_GET['id'])) {
            $id = clean_input($_GET['id']);
            $query = "DELETE FROM mobil WHERE id_mobil = '$id'";
            
            if (mysqli_query($conn, $query)) {
                echo json_encode(['success' => true, 'message' => 'Mobil berhasil dihapus']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menghapus mobil']);
            }
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}
?>