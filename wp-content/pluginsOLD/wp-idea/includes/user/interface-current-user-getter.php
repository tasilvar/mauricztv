<?php namespace bpmj\wpidea\user;

interface Interface_Current_User_Getter
{
    public function get(): ?Interface_User;
}