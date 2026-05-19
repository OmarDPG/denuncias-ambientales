<?php

namespace App\Controllers;

use App\Models\ArchivosDenunciasModel;
use App\Models\DenunciasModel;

class Inicio extends BaseController
{    
    protected $denunciasModel;
    protected $archivosDenunciasModel;
    public function __construct()
    {
        $this->denunciasModel = new DenunciasModel();
        $this->archivosDenunciasModel = new ArchivosDenunciasModel();
    }


    public function index()
    {
        $denuncias = $this->denunciasModel->findAll();
        $data = [
            'denuncias' => $denuncias
        ];
        echo view('inicio/header');
        echo view('inicio/index', $data);
        echo view('inicio/footer');
    }
}