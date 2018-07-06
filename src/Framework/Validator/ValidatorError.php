<?php
namespace Framework\Validator;

class ValidatorError
{

    private $key;
    private $rule;
    private $attributes;


    private $messages = [
    'required'=>'le champ %s est requis',
    'empty'=>'le champ %s ne peut être vide',
    'slug'=>'le champ %s n\'est pas un slug valide',
    'minLength'=>'le champs %s doit contenir plus de %d caractères',
    'maxLength'=>'le champs %s doit contenir moins de %d caractères',
    'betweenLength'=>'le champs %s doit contenir entre %d et %d',
    'datetime'=>'le champs %s doit être une date valide (%s)',
    'exists'=>'le champs %s n\'existe pas dans la table %s',
    'unique'=>'le champs %s doit être unique',
    'filetype'=>'le champs %s n\'est pas au format valide (%s)',
    'uploaded'=>'Vous devez uploader un fichier',
    'email'=>'Cet email ne semble pas valide'
    ];

    public function __construct(string $key, string $rule, array $attributes = [])
    {
        $this->key=$key;
        $this->rule=$rule;
        $this->attributes=$attributes;
    }

    public function __toString()
    {
        $params = array_merge([$this->messages[$this->rule],$this->key], $this->attributes);
        return (string)call_user_func_array('sprintf', $params);
    }
}
