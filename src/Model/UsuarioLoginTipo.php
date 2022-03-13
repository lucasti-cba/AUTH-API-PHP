<?php

App::uses('AppModel', 'Model');
/**
 * UsuarioLoginTipo Model.
 *
 * @property UsuarioLogin $UsuarioLogin
 */
class UsuarioLoginTipo extends AppModel
{
    const LOGIN     = 1;
    const LOGOUT    = 2;
    const ERRO      = 3;
    const BLOQUEADO = 4;

    public static $tipos = [
        self::LOGIN     => 'Login',
        self::LOGOUT    => 'Logout',
        self::ERRO      => 'Erro',
        self::BLOQUEADO => 'Bloqueado',
    ];

    public $displayField = 'nome';

    public $validate = [
        'nome' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
        ],
    ];

    public $hasMany = [
        'UsuarioLogin' => [
            'className' => 'UsuarioLogin',
            'foreignKey' => 'usuario_login_tipo_id',
        ],
    ];

    public function getAll()
    {
        return self::$tipos;
    }

    public function getLoginId()
    {
        return self::LOGIN;
    }

    public function getLogoutId()
    {
        return self::LOGOUT;
    }

    public function getErroId()
    {
        return self::ERRO;
    }

    public function getBloqueadoId()
    {
        return self::BLOQUEADO;
    }
}
