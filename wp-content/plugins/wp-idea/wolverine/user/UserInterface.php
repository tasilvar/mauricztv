<?php
namespace bpmj\wpidea\wolverine\user;

interface UserInterface
{
    public static function find($id);
    public static function findByLogin($login);

    public function load($id);
    public function loadByLogin($login);

    public function getId();
    public function setId($id);

    public function getLogin();
    public function setLogin($login);

    public function getFirstName();
    public function getLastName();
    public function getEmail();

    public function getPasswordResetLink();

    public function can(string $capability): bool;
}