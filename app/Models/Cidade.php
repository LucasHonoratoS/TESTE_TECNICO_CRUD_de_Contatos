<?php

class Cidade
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function allByEstado(int $bro_id): array
    {
        $sql = "SELECT bre_id, bre_nome FROM brasil_cidades WHERE bro_id = :bro_id ORDER BY bre_nome";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':bro_id', $bro_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
