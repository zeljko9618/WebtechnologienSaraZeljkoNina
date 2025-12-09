<?php
namespace Model;

use JsonSerializable;

class Friend implements JsonSerializable {
    private $username;
    private $status;

    public function __construct($username = null) {
        $this->username = $username;
        $this->status = null;
    }

    // Getter
    public function getUsername() {
        return $this->username;
    }

    public function getStatus() {
        return $this->status;
    }

    // Setter
    public function setUsername($username) {
        $this->username = $username;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    // Status-Methoden
    public function accept() {
        $this->status = "accepted";
    }

    public function dismiss() {
        $this->status = "dismissed";
    }

    // JSON Serialization
    public function jsonSerialize(): mixed {
        return get_object_vars($this);
    }

    // JSON Deserialization
    public static function fromJson($data) {
        $friend = new Friend();
        foreach ($data as $key => $value) {
            $friend->{$key} = $value;
        }
        return $friend;
    }
}
?>