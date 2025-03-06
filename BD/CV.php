<?php
require_once 'Connexion.php';

function getAllCVs() {
    $connexion = getConnexion();
    
    $query = "SELECT id, CONCAT(first_name, ' ', last_name) as name, email, phone, job_title, created_at 
              FROM cvs 
              ORDER BY created_at DESC";
    $stmt = $connexion->prepare($query);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCVById($id) {
    $connexion = getConnexion();
    
    $query = "SELECT id, first_name, last_name, job_title, email, phone, 
                     address, postal_code, city, description, photo_path,
                     education, experience, skills, languages, created_at 
              FROM cvs 
              WHERE id = :id";
    $stmt = $connexion->prepare($query);
    $stmt->execute(['id' => $id]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createCV($data) {
    $connexion = getConnexion();
    
    try {
        $query = "INSERT INTO cvs (
            first_name, last_name, job_title, email, phone,
            address, postal_code, city, description, photo_path,
            education, experience, skills, languages
        ) VALUES (
            :first_name, :last_name, :job_title, :email, :phone,
            :address, :postal_code, :city, :description, :photo_path,
            :education, :experience, :skills, :languages
        )";
        
        $stmt = $connexion->prepare($query);
        $result = $stmt->execute([
            'first_name' => $data['prenom'],
            'last_name' => $data['nom'],
            'job_title' => $data['titre'],
            'email' => $data['email'],
            'phone' => $data['telephone'],
            'address' => $data['adresse'],
            'postal_code' => $data['code_postal'],
            'city' => $data['ville'],
            'description' => $data['description'],
            'photo_path' => $data['photo']['tmp_name'],
            'education' => json_encode($data['formations']),
            'experience' => json_encode($data['experiences']),
            'skills' => json_encode($data['competences']),
            'languages' => json_encode($data['langues'])
        ]);
        
        if ($result) {
            return $connexion->lastInsertId();
        }
        return false;
        
    } catch (PDOException $e) {
        throw $e;
    }
}

function deleteCV($id) {
    $connexion = getConnexion();
    
    // First get the CV to delete the photo if exists
    $cv = getCVById($id);
    if ($cv && !empty($cv['photo_path']) && file_exists($cv['photo_path'])) {
        unlink($cv['photo_path']);
    }
    
    $query = "DELETE FROM cvs WHERE id = :id";
    $stmt = $connexion->prepare($query);
    return $stmt->execute(['id' => $id]);
}

function updateCV($id, $data) {
    $connexion = getConnexion();
    
    $fields = [];
    $params = ['id' => $id];
    
    foreach ($data as $key => $value) {
        if ($key !== 'id') {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }
    }
    
    $query = "UPDATE cvs SET " . implode(', ', $fields) . " WHERE id = :id";
    $stmt = $connexion->prepare($query);
    return $stmt->execute($params);
} 