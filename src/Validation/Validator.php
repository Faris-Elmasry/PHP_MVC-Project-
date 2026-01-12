<?php

namespace Elmasry\Validation;


class Validator
{
    protected array $errors = [];
   
    protected array $rules = [];


    protected array $data = [];
    protected array $aliases = [];

    protected ErrorBag $errorBag;

public function make($data){
$this->data =$data ;
$this->errorBag =new ErrorBag;
$this->validate();

}
protected function  validate() {

foreach ($this->rules as $field => $rule){
var_dump($field ,$rule);

}
}

  


public function setRules($rules){
    $this->rules = $rules;
}

public function passes(){
    return empty($this->errors());

}

public function errors($key =null){
    return  $key ? $this->errorBag->errors[$key] :  $this->errorBag->errors ;
}


public function alias($field ){

    return $this->aliases[$field] ?? $field;

}
    
public function setAliases(array $aliases){

    $this->aliases =$aliases;

}
}