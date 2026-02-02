<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get all users or single user
        if (isset($_GET['id'])) {
            $id = clean_input($_GET['id']);
            $query = "SELECT id_user, nama, email, no_hp, created_at FROM user WHERE id_user = '$id'";
            $result = mysqli_query($conn, $query);
            $data = mysqli_fetch_assoc($result);
            echo json_encode($data);
        } else {
            $query = "SELECT id_user, nama, email, no_hp, created_at FROM user ORDER BY created_at DESC";
            $result = mysqli_query($conn, $query);
            $data = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            echo json_encode($data);
        }
        break;
        
    case 'POST':
        // Register new user
        $input = json_decode(file_get_contents('php://input'), true);
        
        $nama = clean_input($input['nama']);
        $email = clean_input($input['email']);
        $password = password_hash($input['password'], PASSWORD_DEFAULT);
        $no_hp = clean_input($input['no_hp']);
        
        // Check if email exists
        $check = mysqli_query($conn, "SELECT * FROM user WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            echo json_encode(['success' => false, 'message' => 'Email sudah terdaftar']);
            exit;
        }
        
        $query = "INSERT INTO user (nama, email, password, no_hp) VALUES ('$nama', '$email', '$password', '$no_hp')";
        
        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true, 'message' => 'User berhasil didaftarkan', 'id' => mysqli_insert_id($conn)]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mendaftarkan user']);
        }
        break;
        
    case 'PUT':
        // Update user
        $input = json_decode(file_get_contents('php://input'), true);
        
        $id = clean_input($input['id_user']);
        $nama = clean_input($input['nama']);
        $email = clean_input($input['email']);
        $no_hp = clean_input($input['no_hp']);
        
        $query = "UPDATE user SET nama='$nama', email='$email', no_hp='$no_hp' WHERE id_user='$id'";
        
        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true, 'message' => 'User berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengupdate user']);
        }
        break;
        
    case 'DELETE':
        // Delete user
        if (isset($_GET['id'])) {
            $id = clean_input($_GET['id']);
            $query = "DELETE FROM user WHERE id_user = '$id'";
            
            if (mysqli_query($conn, $query)) {
                echo json_encode(['success' => true, 'message' => 'User berhasil dihapus']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menghapus user']);
            }
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}
?>