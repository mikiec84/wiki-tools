<?php
ORM::configure('mysql:host=localhost;dbname=' . DB_TOOLSDIRECTORY);
ORM::configure('username', DB_USER);
ORM::configure('password', DB_PASS);

class Tool extends Model {
    public static $_table = "tools";

    public function update($data) {
        foreach ($data as $key => $value) {
            if ($key == "keywords") {
                $value = implode(",", $value);
            }

            $this->set($key, $value);
        }

        $this->save();
    }
}