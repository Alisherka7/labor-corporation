<?php

class DB
{
    public $conn;
    public $tablename;
    public function __construct($host, $username, $passwd, $dbname, $tablename, $origin)
    {
        $this->tablename = $tablename;

        $this->conn = new mysqli($host, $username, $passwd);
        $querys = [
            "CREATE DATABASE IF NOT EXISTS {$dbname}",
            "USE {$dbname}",
            "CREATE TABLE IF NOT EXISTS {$this->tablename}(metakey VARCHAR(255) PRIMARY KEY, metadata TEXT)",
            
        ];
        if($origin) $querys[] = "INSERT INTO {$this->tablename} (metakey) VALUES('origin')";
        foreach ($querys as $q) {
            $this->conn->query($q);
        }
    }

    public function Add($metakey)
    {
        return $this->conn->query("INSERT INTO {$this->tablename} (metakey) VALUES('{$metakey}')");
    }

    public function Remove($metakey)
    {
        return $this->conn->query("DELETE FROM {$this->tablename} WHERE metakey='{$metakey}'");
    }

    public function Get($metakey)
    {
        // $metakey = "user";
        $r = $this->conn->query("SELECT metadata FROM {$this->tablename} WHERE metakey='{$metakey}'");
        $result = json_decode(mysqli_fetch_assoc($r)['metadata'], true);
        // echo "SELECT metadata FROM {$this->tablename} WHERE metakey='{$metakey}'";
        return $result;
    }

    public function GetAll()
    {
        $tempResult = $this->conn->query("SELECT * FROM {$this->tablename}");
        $fa = [];
        while ($row = $tempResult->fetch_assoc()) {
            $fa[] = $row;
            // do what you need.
        }
        // $fa = mysqli_fetch_all($r, true);
        $result = [];
        foreach ($fa as $f) {
            if ($f['metakey'] === 'origin') {
                continue;
            }

            $result[$f['metakey']] = json_decode($f['metadata'], true);
        }

        return $result;
    }

    public function Set($metakey, $metadata)
    {
        $metadata = addslashes(json_encode($metadata, JSON_UNESCAPED_UNICODE));
        return $this->conn->query("UPDATE {$this->tablename} SET metadata='{$metadata}' WHERE metakey='{$metakey}'");
    }

    public function GetOrigin()
    {
        return $this->Get('origin');
    }

    public function SetOrigin($metadata)
    {
        return $this->Set('origin', $metadata);
    }
}
