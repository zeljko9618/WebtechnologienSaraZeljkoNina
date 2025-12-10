<?php
namespace Model;

use JsonSerializable;

class Friend implements JsonSerializable
{
    private $username;
    private $status;

    public function __construct($username = null, $status = null)
    {
        $this->username = $username;
        $this->status   = $status;
    }

    // Getter
    public function getUsername()
    {
        return $this->username;
    }

    public function getStatus()
    {
        return $this->status;
    }

    // Setter (from Nina)
    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    // Status ändern
    public function accept()
    {
        $this->status = "accepted";
    }

    public function dismiss()
    {
        $this->status = "dismissed";
    }

    // JSON Serialisierung
    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }

    // JSON → Objekt
    public static function fromJson($data): Friend
    {
        $friend = new Friend();

        foreach ($data as $key => $value) {
            $friend->{$key} = $value;
        }

        return $friend;
    }
}
