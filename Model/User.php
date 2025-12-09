<?php
namespace Model;

use JsonSerializable;

class User implements JsonSerializable {
    private $username;
    private $firstName;
    private $lastName;
    private $coffeOrTea;
    private $description;
    private $history;

    public function __construct($username = null) {
        $this->username = $username;
        $this->firstName = null;
        $this->lastName = null;
        $this->coffeOrTea = null;
        $this->description = null;
        $this->history = array();
    }

    // Getter
    public function getUsername() {
        return $this->username;
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getCoffeOrTea() {
        return $this->coffeOrTea;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getHistory() {
        return $this->history;
    }

    // Setter
    public function setUsername($username) {
        $this->username = $username;
    }

    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    public function setCoffeOrTea($coffeOrTea) {
        $this->coffeOrTea = $coffeOrTea;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setHistory($history) {
        $this->history = $history;
    }

    // JSON Serialization
    public function jsonSerialize(): mixed {
        return get_object_vars($this);
    }

    // JSON Deserialization
    public static function fromJson($data) {
        $user = new User();
        foreach ($data as $key => $value) {
            $user->{$key} = $value;
        }
        return $user;
    }
}
?>