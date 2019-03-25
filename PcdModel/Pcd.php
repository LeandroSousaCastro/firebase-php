<?php

require __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class Pcd
{
    protected $database;
    protected $dbname = 'dados-pcds';

    public function __construct()
    {
        $acc = ServiceAccount::fromJsonFile(__DIR__ . '/../angular-firebase-92892-8c2cfdfabc3e.json');
        $firebase = (new Factory)->withServiceAccount($acc)->create();
        $this->database = $firebase->getDatabase();
    }

    public function getAll() {
        return $this->database->getReference($this->dbname)->getValue();
    }

    public function get($pcdID = null)
    {
        if (empty($pcdID) || !isset($pcdID)) {
            return false;
        }
        if ($this->database->getReference($this->dbname)->getSnapshot()->hasChild($pcdID)) {
            return $this->database->getReference($this->dbname)->getChild($pcdID)->getValue();
        } else {
            return false;
        }
    }

    public function insert(array $data)
    {
        if (empty($data) || !isset($data)) {
            return false;
        }
        foreach ($data as $key => $value) {
            $this->database->getReference()->getChild($this->dbname)->getChild($key)->set($value);
        }
        return true;
    }

    public function update(array $data)
    {
        if (empty($data) || !isset($data)) {
            return false;
        }
        foreach ($data as $key => $value) {
            $this->database->getReference()->getChild($this->dbname)->getChild($key)->update($value);
        }
        return true;
    }

    public function delete($pcdID)
    {
        if (empty($pcdID) || !isset($pcdID)) {
            return false;
        }
        if ($this->database->getReference($this->dbname)->getSnapshot()->hasChild($pcdID)) {
            $this->database->getReference($this->dbname)->getChild($pcdID)->remove();
            return true;
        } else {
            return false;
        }
    }
}