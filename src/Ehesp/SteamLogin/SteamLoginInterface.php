<?php
namespace Ehesp\SteamLogin;

interface SteamLoginInterface
{
    public function url($return);
	public function validate();
}
