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
    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function getTelefoneFixo()
    {
        return $this->telefone_fixo;
    }
    public function setTelefoneFixo($telefone_fixo)
    {
        $this->telefone_fixo = $telefone_fixo;
    }
    public function getTelefoneCelular()
    {
        return $this->telefone_celular;
    }
    public function setTelefoneCelular($telefone_celular)
    {
        $this->telefone_celular = $telefone_celular;
    }


}
