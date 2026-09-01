<?php namespace App\Controllers;

class Inicio extends BaseController
{
    public function index(): string
    {
        return view('home');
    }
}