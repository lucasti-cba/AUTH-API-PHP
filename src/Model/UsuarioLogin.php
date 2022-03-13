<?php

App::uses('AppModel', 'Model');
App::uses('Utils', 'Lib');

/**
 * UsuarioLogin Model.
 *
 * @property Usuario $Usuario
 * @property UsuarioLoginTipo $UsuarioLoginTipo
 */
class UsuarioLogin extends AppModel
{
    public $order = ['data' => 'asc'];

    public $validate = [
        'usuario_id' => [
            'decimal' => [
                'rule' => ['decimal'],
                'allowEmpty' => true,
            ],
        ],
        'usuario' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
            'maxLength' => [
                'rule' => ['maxLength', 60],
                'message' => 'Tamanho máximo 60 caracteres.',
            ],
        ],
        'usuario_login_tipo_id' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
            'decimal' => [
                'rule' => ['decimal'],
            ],
        ],
    ];

    public $belongsTo = [
        'Usuario' => [
            'className' => 'Usuario',
            'foreignKey' => 'usuario_id',
        ],
        'UsuarioLoginTipo' => [
            'className' => 'UsuarioLoginTipo',
            'foreignKey' => 'usuario_login_tipo_id',
        ],
        'TipoUsuario' => [
            'className' => 'TipoUsuario',
            'foreignKey' => 'tipo_usuario_id',
        ],
        'Instituicao' => [
            'className' => 'Instituicao',
            'foreignKey' => 'instituicao_id',
        ],
    ];

    public function afterFind($results = [], $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val['UsuarioLogin']['data'])) {
                $results[$key]['UsuarioLogin']['data_formatada'] = $this->formateDateToPtBr($val['UsuarioLogin']['data'], true);
            } else {
                $results[$key]['UsuarioLogin']['data_formatada'] = null;
            }
        }

        return $results;
    }

    /**
     * Gravar auditoria de acessos.
     *
     * @param [string] $usuario       nome
     * @param [int]    $tipoId        tipo de acesso
     * @param [int]    $tipoUsuarioId id do tipo do usuário
     * @param [int]    $instituicaoId id da instituição
     *
     * @return [boolean] sucesso ou erro na operação
     */
    public function gravarAcesso($usuario, $tipoId, $tipoUsuarioId = null, $instituicaoId = null)
    {
        $user = $this->Usuario->findByUsuario($usuario);

        $usuarioId = (isset($user['Usuario']['id'])) ? $user['Usuario']['id'] : null;

        $this->create();
        $this->set('usuario_id', $usuarioId);
        $this->set('usuario', $usuario);
        $this->set('usuario_login_tipo_id', $tipoId);
        $this->set('tipo_usuario_id', $tipoUsuarioId);
        $this->set('instituicao_id', $instituicaoId);

        return $this->save();
    }

    /**
     * Registrar log de acesso ao sistema.
     *
     * @param [int] $usuario       Nome do usuário
     * @param [int] $tipoUsuarioId Id do tipo de usuário
     * @param [int] $instituicaoId Id dá instituição de usuário
     *
     * @return [boolean] sucesso ou erro na operação
     */
    public function gravarLogin($usuario, $tipoUsuarioId, $instituicaoId)
    {
        $tipoId = $this->UsuarioLoginTipo->getLoginId();

        return $this->gravarAcesso($usuario, $tipoId, $tipoUsuarioId, $instituicaoId);
    }

    /**
     * Registrar log de erro ao acessar o sistema.
     *
     * @param [int] $usuario Nome do usuário
     *
     * @return [boolean] sucesso ou erro na operação
     */
    public function gravarErro($usuario)
    {
        $tipoId = $this->UsuarioLoginTipo->getErroId();

        return $this->gravarAcesso($usuario, $tipoId);
    }

    /**
     * Registrar log de usuário bloquado ao tentar acessar o sistema.
     *
     * @param [int] $usuario Nome do usuário
     *
     * @return [boolean] sucesso ou erro na operação
     */
    public function gravarBloqueado($usuario)
    {
        $tipoId = $this->UsuarioLoginTipo->getBloqueadoId();

        return $this->gravarAcesso($usuario, $tipoId);
    }

    /**
     * Registrar log de usuário saindo do sistema.
     *
     * @param [int] $usuario       Nome do usuário
     * @param [int] $tipoUsuarioId Id do tipo de usuário
     * @param [int] $instituicaoId Id dá instituição de usuário
     *
     * @return [boolean] sucesso ou erro na operação
     */
    public function gravarLogout($usuario, $tipoUsuarioId, $instituicaoId)
    {
        $tipoId = $this->UsuarioLoginTipo->getLogoutId();

        return $this->gravarAcesso($usuario, $tipoId, $tipoUsuarioId, $instituicaoId);
    }

    public function getByUsuarioIdBetweenDates($usuarioId, $dataInicio, $dataFim)
    {
        $tiposId = [$this->UsuarioLoginTipo->getLoginId(), $this->UsuarioLoginTipo->getLogoutId()];

        return $this->find('all', [
            'recursive' => -1,
            'conditions' => [
                'UsuarioLogin.usuario_id' => $usuarioId,
                'UsuarioLogin.data between ? and ?' => [$dataInicio, $dataFim],
                'UsuarioLogin.usuario_login_tipo_id' => $tiposId,
            ],
        ]);
    }

    /**
     * Pega os dados de logins registrados entre duas datas.
     *
     * @param $string dataInicial data minima em formato BR
     * @param $string dataFinal data maxima em formato BR
     * @param mixed $dataInicio
     * @param mixed $dataFim
     *
     * @return array
     */
    public function pegaDadosLogin($dataInicio, $dataFim)
    {
        $tiposId = $this->UsuarioLoginTipo->getLoginId();
        $opt = [
            'recursive' => -1,
            'fields' => [
                'UsuarioLogin.usuario_id',
                'UsuarioLogin.tipo_usuario_id',
                'UsuarioLogin.instituicao_id',
                'COUNT(UsuarioLogin.usuario_id)',
            ],
            'conditions' => [
                'UsuarioLogin.usuario_login_tipo_id' => $tiposId,
            ],
            'group' => [
                'UsuarioLogin.usuario_id',
                'UsuarioLogin.tipo_usuario_id',
                'UsuarioLogin.instituicao_id',
            ],
            'order' => ['UsuarioLogin.usuario_id'],
        ];

        if ($dataInicio) {
            $opt['conditions']['UsuarioLogin.data >='] = Utils::formataDataUnix($dataInicio) . ' 00:00:00';
        }
        if ($dataFim) {
            $opt['conditions']['UsuarioLogin.data <='] = Utils::formataDataUnix($dataFim) . ' 23:59:59';
        }
        $dados = $this->find('all', $opt);

        return $dados;
    }
}
