<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOtpFieldsToDenuncias extends Migration
{
    public function up()
    {
        $fields = [
            'codigo_verificacion' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Hash del código OTP de verificación'
            ],
            'codigo_verificacion_expira' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Fecha y hora de expiración del código OTP'
            ],
            'intentos_verificacion' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Número de intentos fallidos de verificación'
            ],
            'verificado_en' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Fecha y hora en que se verificó la denuncia'
            ],
            'ultimo_envio_codigo' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Fecha y hora del último envío de código (rate limiting)'
            ]
        ];

        $this->forge->addColumn('denuncias', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('denuncias', [
            'codigo_verificacion',
            'codigo_verificacion_expira',
            'intentos_verificacion',
            'verificado_en',
            'ultimo_envio_codigo'
        ]);
    }
}
