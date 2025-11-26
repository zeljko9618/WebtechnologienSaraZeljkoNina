<?php
namespace Model;

use JsonSerializable;

class User implements JsonSerializable
{
    // Attribute
    private $username;
    // Nina hier ergänzst du weitere Attribute in Teilaufgabe i und j
    // z.B. $fullname, $gender, $history usw

    // Konstruktor
    public function __construct($username = null)
    {
        $this->username = $username;
    }

    // Getter
    public function getUsername()
    {
        return $this->username;
    }

    // JSON-Serialisierung
    public function jsonSerialize(): mixed
    {
        // Alle Objekt-Attribute als Array zurückgeben
        return get_object_vars($this);
    }

    // Von JSON nach User-Objekt
    public static function fromJson($data): User
    {
        $user = new User();

        foreach ($data as $key => $value) {
            $user->{$key} = $value;
        }

        return $user;
    }
}
