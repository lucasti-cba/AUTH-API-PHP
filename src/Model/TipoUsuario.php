<?php

class TipoUsuario extends AppModel
{
    public $name = 'TipoUsuario';
    public $displayField = 'nome';
    public $order = ['TipoUsuario.nome' => 'ASC'];
    public $validate = [
        'nome' => [
            'notBlank' => [
                'rule'     => ['notBlank'],
                'message'  => 'O Nome não pode estar vazio!',
            ],
            'unique' => [
                'rule'    => ['isUnique'],
                'message' => 'Já existe um perfil com esse nome.',
                'allowEmpty' => true,
            ],
        ],
    ];

    public $hasMany = [
        'InstituicaoUsuario' => [
            'className' => 'InstituicaoUsuario',
            'foreignKey' => 'tipo_usuario_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => '',
        ],
        'ActionTipoUsuario' => [
            'className' => 'ActionTipoUsuario',
            'foreignKey' => 'tipo_usuario_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => '',
        ],
    ];

    public $actsAs = ['Log', 'Linkable'];

    public function exportar($id)
    {
        $this->InstituicaoUsuario = ClassRegistry::init('InstituicaoUsuario');
        $this->ActionTipoUsuario  = ClassRegistry::init('ActionTipoUsuario');

        $tipoUsuario = $this->find('first', [
            'recursive' => -1,
            'fields' => [
                'TipoUsuario.nome',
            ],
            'conditions' => [
                'TipoUsuario.id' => $id,
            ],
        ]);
        if (empty($tipoUsuario)) {
            return false;
        }

        $dados = $tipoUsuario['TipoUsuario'];
        $dados['instituicoes'] = [];
        $dados['actions']      = [];

        $instituicaoUsuario = $this->InstituicaoUsuario->find('all', [
            'recursive' => -1,
            'fields' => [
                'InstituicaoUsuario.instituicao_id',
                'InstituicaoUsuario.usuario_id',
                'InstituicaoUsuario.orgao_default',
            ],
            'link' => [
                'Instituicao' => [
                    'class' => 'Instituicao',
                    'type'   => 'left',
                    'fields' => [
                        'Instituicao.id',
                        'Instituicao.nome',
                    ],
                    'conditions' => [
                        'InstituicaoUsuario.instituicao_id = Instituicao.id',
                    ],
                ],
                'Usuario' => [
                    'class' => 'Usuario',
                    'type'   => 'left',
                    'fields' => [
                        'Usuario.id',
                        'Usuario.usuario',
                    ],
                    'conditions' => [
                        'InstituicaoUsuario.usuario_id = Usuario.id',
                    ],
                ],
            ],
            'conditions' => [
                'InstituicaoUsuario.tipo_usuario_id' => $id,
            ],
        ]);
        foreach ($instituicaoUsuario as $atual) {
            $dados['instituicoes'][] = [
                'orgao_default'  => $atual['InstituicaoUsuario']['orgao_default'],
                'instituicao'    => $atual['Instituicao']['nome'],
                'usuario'        => $atual['Usuario']['usuario'],
            ];
        }

        $actionTipoUsuario = $this->ActionTipoUsuario->find('all', [
            'recursive' => -1,
            'fields' => [
                'ActionTipoUsuario.id',
                'ActionTipoUsuario.action_id',
            ],
            'link' => [
                'Action' => [
                    'class' => 'Action',
                    'type'   => 'left',
                    'fields' => [
                        'Action.id',
                        'Action.nome',
                        'Action.control_id',
                    ],
                    'conditions' => [
                        'ActionTipoUsuario.action_id = Action.id',
                    ],
                ],
                'Control' => [
                    'class' => 'Control',
                    'type'   => 'left',
                    'fields' => [
                        'Control.id',
                        'Control.nome',
                    ],
                    'conditions' => [
                        'Control.id = Action.control_id',
                    ],
                ],
            ],
            'conditions' => [
                'ActionTipoUsuario.tipo_usuario_id' => $id,
            ],
        ]);
        foreach ($actionTipoUsuario as $atual) {
            $dados['actions'][] = [
                'action'        => $atual['Action']['nome'],
                'control'       => $atual['Control']['nome'],
            ];
        }

        return $dados;
    }

    private function existeNome($nome)
    {
        $existe = $this->find('count', [
            'conditions' => ['TipoUsuario.nome' => $nome],
        ]);

        return !empty($existe);
    }

    private $cacheUsuarios = [];

    private function descobreUsuarioIdCorrespondente($usuario)
    {
        if (!isset($this->cacheUsuarios[$usuario])) {
            $this->Usuario = ClassRegistry::init('Usuario');
            $dados = $this->Usuario->find('first', [
                'recursive'  => -1,
                'fields'     => ['Usuario.id'],
                'conditions' => ['Usuario.usuario' => $usuario],
            ]);

            $this->cacheUsuarios[$usuario] = isset($dados['Usuario']['id'])
                ? $dados['Usuario']['id']
                : null
            ;
        }

        return $this->cacheUsuarios[$usuario];
    }

    private $cacheInstituicoes = [];

    private function descobreInstituicaoIdCorrespondente($instituicao)
    {
        if (!isset($this->cacheInstituicoes[$instituicao])) {
            $this->Instituicao = ClassRegistry::init('Instituicao');
            $dados = $this->Instituicao->find('first', [
                'recursive'  => -1,
                'fields'     => ['Instituicao.id'],
                'conditions' => ['Instituicao.nome' => $instituicao],
            ]);

            $this->cacheInstituicoes[$instituicao] = isset($dados['Instituicao']['id'])
                ? $dados['Instituicao']['id']
                : null
            ;
        }

        return $this->cacheInstituicoes[$instituicao];
    }

    private $cacheControl = [];

    private function descobreControlIdCorrespondente($control)
    {
        if (!isset($this->cacheControl[$control])) {
            $this->Control = ClassRegistry::init('Control');
            $dados = $this->Control->find('first', [
                'recursive'  => -1,
                'fields'     => ['Control.id'],
                'conditions' => ['Control.nome' => $control],
            ]);

            $this->cacheControl[$control] = isset($dados['Control']['id'])
                ? $dados['Control']['id']
                : null
            ;
        }

        return $this->cacheControl[$control];
    }

    private function descobreActionIdCorrespondente($action, $controlId)
    {
        $this->Action = ClassRegistry::init('Action');
        $dados = $this->Action->find('first', [
            'recursive'  => -1,
            'fields'     => ['Action.id'],
            'conditions' => ['Action.control_id' => $controlId, 'Action.nome' => $action],
        ]);

        return isset($dados['Action']['id'])
            ? $dados['Action']['id']
            : null
        ;
    }

    public function importar($dados)
    {
        $this->InstituicaoUsuario = ClassRegistry::init('InstituicaoUsuario');
        $this->ActionTipoUsuario = ClassRegistry::init('ActionTipoUsuario');
        // checar se ja existe tipo_usuario com este nome
        if ($this->existeNome($dados['nome'])) {
            $type = $this->find('first', [
                'conditions' => ['TipoUsuario.nome' => $dados['nome']]
            ]);

            $id = $type['TipoUsuario']['id'];
        } else {

            $this->create();
            $this->save(['nome' => $dados['nome']]);

            $id = $this->id;
        }

        // checando instituicao_usuarios que podem ser importados
        $instituicaoUsuario = [];
        foreach ($dados['instituicoes'] as $atual) {
            $usuarioId = $this->descobreUsuarioIdCorrespondente(utf8_decode($atual['usuario']));
            $instituicaoId = $this->descobreInstituicaoIdCorrespondente(utf8_decode($atual['instituicao']));
            // se não existe usuario ou instituicao correspondente no banco, não da pra importar essa relação
            if (empty($usuarioId) || empty($instituicaoId)) {
                // TODO informar ao usuário que uma atribuição não pode ser feita?
                continue;
            }

            // está tudo ok, pode importar
            $instituicaoUsuario[] = [
                'tipo_usuario_id' => $id,
                'instituicao_id'  => $instituicaoId,
                'usuario_id'      => $usuarioId,
                'orgao_default'   => $atual['orgao_default'],
            ];
        }
        // salvando importações de instituicao_usuarios
        if (!empty($instituicaoUsuario)) {
            $this->InstituicaoUsuario->create();
            $this->InstituicaoUsuario->saveMany($instituicaoUsuario);
        }

        // checando action_tipo_usuarios que podem ser importados
        $actions = [];
        foreach ($dados['actions'] as $atual) {
            $controlId = $this->descobreControlIdCorrespondente($atual['control']);
            if (empty($controlId)) {
                continue;
            }

            $actionId  = $this->descobreActionIdCorrespondente($atual['action'], $controlId);
            // se não controler ou action correspondente no banco, não pode importar
            if (empty($actionId)) {
                // TODO informar ao usuário que uma action não foi associada?
                continue;
            }

            $imported = $this->ActionTipoUsuario->find('first', [
                'conditions' => [
                    'tipo_usuario_id' => $id,
                    'action_id' => $actionId
                ]
            ]);

            if (empty($imported)) {
                // está tudo ok, pode importar
                $actions[] = [
                    'tipo_usuario_id' => $id,
                    'action_id'  => $actionId,
                ];
            }
        }
        // salvando importações de instituicao_usuarios
        if (!empty($actions)) {
            $this->ActionTipoUsuario->create();
            $this->ActionTipoUsuario->saveMany($actions);
        }

        return true;
    }
}
