<?php
require_once __DIR__ . '/../config/database.php';

class Alumni {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM alumni ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM alumni WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO alumni (name, contact) VALUES (?, ?)");
        return $stmt->execute([$data['name'], $data['contact'] ?? null]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE alumni SET name = ?, contact = ? WHERE id = ?");
        return $stmt->execute([$data['name'], $data['contact'] ?? null, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM alumni WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
