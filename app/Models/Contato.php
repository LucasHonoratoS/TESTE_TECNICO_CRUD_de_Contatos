<?php

class Contato
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function all(array $filters = []): array
    {
        $sql = "
            SELECT 
                c.con_id,
                c.con_nome,
                c.con_telefone,
                c.con_cpf,
                c.bro_id,
                c.bre_id,
                e.bro_sigla,
                e.bro_nome,
                cid.bre_nome
            FROM contatos c
            INNER JOIN brasil_estados e ON e.bro_id = c.bro_id
            INNER JOIN brasil_cidades cid ON cid.bre_id = c.bre_id
            WHERE 1 = 1
        ";

        $params = [];

        if (!empty($filters['nome'])) {
            $sql .= " AND c.con_nome LIKE :nome";
            $params[':nome'] = '%' . $filters['nome'] . '%';
        }

        if (!empty($filters['telefone'])) {
            $telFilter = sanitize_telefone($filters['telefone']);
            if ($telFilter !== '') {
                $sql .= " AND c.con_telefone LIKE :telefone";
                $params[':telefone'] = '%' . $telFilter . '%';
            }
        }

        if (!empty($filters['cpf'])) {
            $sql .= " AND c.con_cpf LIKE :cpf";
            $params[':cpf'] = '%' . $filters['cpf'] . '%';
        }

        if (!empty($filters['bro_id'])) {
            $sql .= " AND c.bro_id = :bro_id";
            $params[':bro_id'] = (int) $filters['bro_id'];
        }

        if (!empty($filters['bre_id'])) {
            $sql .= " AND c.bre_id = :bre_id";
            $params[':bre_id'] = (int) $filters['bre_id'];
        }

        $sql .= " ORDER BY c.con_nome ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $sql = "
            SELECT 
                c.con_id,
                c.con_nome,
                c.con_telefone,
                c.con_cpf,
                c.bro_id,
                c.bre_id,
                e.bro_sigla,
                e.bro_nome,
                cid.bre_nome
            FROM contatos c
            INNER JOIN brasil_estados e ON e.bro_id = c.bro_id
            INNER JOIN brasil_cidades cid ON cid.bre_id = c.bre_id
            WHERE c.con_id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $contato = $stmt->fetch();

        return $contato ?: null;
    }

    public function create(array $data): int
    {
        $sql = "
            INSERT INTO contatos (con_nome, con_telefone, con_cpf, bro_id, bre_id)
            VALUES (:con_nome, :con_telefone, :con_cpf, :bro_id, :bre_id)
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':con_nome', $data['con_nome']);
        $stmt->bindValue(':con_telefone', $data['con_telefone']);
        $stmt->bindValue(':con_cpf', $data['con_cpf']);
        $stmt->bindValue(':bro_id', (int) $data['bro_id'], PDO::PARAM_INT);
        $stmt->bindValue(':bre_id', (int) $data['bre_id'], PDO::PARAM_INT);

        $stmt->execute();

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE contatos
               SET con_nome      = :con_nome,
                   con_telefone  = :con_telefone,
                   con_cpf       = :con_cpf,
                   bro_id        = :bro_id,
                   bre_id        = :bre_id
             WHERE con_id        = :con_id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':con_nome', $data['con_nome']);
        $stmt->bindValue(':con_telefone', $data['con_telefone']);
        $stmt->bindValue(':con_cpf', $data['con_cpf']);
        $stmt->bindValue(':bro_id', (int) $data['bro_id'], PDO::PARAM_INT);
        $stmt->bindValue(':bre_id', (int) $data['bre_id'], PDO::PARAM_INT);
        $stmt->bindValue(':con_id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM contatos WHERE con_id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function existsCpf(string $cpf, ?int $ignoreId = null): bool
    {
        $sql = "SELECT COUNT(*) AS total FROM contatos WHERE con_cpf = :cpf";
        $params = [':cpf' => $cpf];

        if ($ignoreId !== null) {
            $sql .= " AND con_id <> :id";
            $params[':id'] = $ignoreId;
        }

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            if ($key === ':id') {
                $stmt->bindValue($key, (int)$value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->execute();

        $row = $stmt->fetch();

        return ((int)$row['total']) > 0;
    }
}
