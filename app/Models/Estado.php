<?php

class Estado
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function all(): array
    {
        $sql = "SELECT bro_id, bro_sigla, bro_nome FROM brasil_estados ORDER BY bro_nome";
        return $this->db->query($sql)->fetchAll();
    }
}
