<?php

namespace App\Model;

class Usuario 
{
    private $nome;
    private $email;
    private $telefone_fixo;
    private $telefone_celular;
    private $usuario;
    private $senha;
    private $ativo;
    private $local_acesso;
    private $logado;
    private $rg;
    private $cpf;
    private $matricula;
    private $trocar_senha;
    private $created;
    private $modified;
    private $session_key;

    //Getter and Setter
    
    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    



}
