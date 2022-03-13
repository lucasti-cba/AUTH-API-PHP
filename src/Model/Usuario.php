<?php

namespace App\Model;
use BadFunctionCallException;

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
    public function getEmail()
    {
        return $this->email;
    }

    public function getTelefoneFixo()
    {
        return $this->telefone_fixo;
    }

    public function getTelefoneCelular()
    {
        return $this->telefone_celular;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }

    public function getSenha()
    {
       return $this->senha;
    }
    

    public function getAtivo()
    {
        return $this->ativo;
    }

    public function getLocalAcesso()
    {
        return $this->local_acesso;
    }

    public function getLogado()
    {
        return $this->logado;
    }
    function setLogado($logado)
    {
        $this->logado = $logado;
    }
    public function getRG()
    {
        return $this->rg;
    }

    public function getCPF()
    {
        return $this->cpf;
    }

    public function getMatricula()
    {
        return $this->matricula;
    }

    
    public function getTrocarSenha()
    {
        return $this->trocar_senha;
    }
    function setTrocarSenha($trocar_senha)
    {
        $this->trocar_senha = $trocar_senha;
    }

    public function getCreated()
    {
        return $this->created;
    }



}
