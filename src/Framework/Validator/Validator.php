<?php
namespace Framework\Validator;

use Framework\Database\Table;
use Framework\Validator\ValidatorError;
use Psr\Http\Message\UploadedFileInterface;

class Validator
{

    private const MIME_TYPES = [
    'jpg'=>'image/jpeg',
    'png'=>'image/png',
    'pdf'=>'application/pdf'
    ];

    private $params;

    private $errors = [];

    public function __construct(array $params)
    {


        $this->params=$params;
    }

    public function required(string ... $keys):self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value)) {
                $this->addErrors($key, 'required');
            }
        }
        return $this;
    }

    public function notEmpty(string ... $keys)
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value) || empty($value)) {
                $this->addErrors($key, 'empty');
            }
        }

        return $this;
    }


    public function length(string $key, ?int $min, ?int $max = null):self
    {
        $value = $this->getValue($key);
        $lenght = mb_strlen($value);
        if (!is_null($min) && !is_null($max) && ($lenght<$min || $lenght >$max)) {
            $this->addErrors($key, 'betweenLength', [$min,$max]);
            return $this;
        }
        if (!is_null($min) && $lenght < $min) {
            $this->addErrors($key, 'minLength', [$min]);
            return $this;
        }
        if (!is_null($max) && $lenght > $max) {
            $this->addErrors($key, 'maxLength', [$max]);
            return $this;
        }
        return $this;
    }



    public function slug(string $key):self
    {
        $value = $this->getValue($key);
        $pattern ='/^[a-z0-9]+(-[a-z0-9]+)*$/';
        if (!is_null($value) && !preg_match($pattern, $value)) {
            $this->addErrors($key, 'slug');
        }
        return $this;
    }

    public function dateTime(string $key, string $format = 'Y-m-d H:i:s'):self
    {
        $value=$this->getValue($key);
        $date = \DateTime::createFromFormat($format, $value);
        $errors = \DateTime::getLastErrors();
        if ($errors['error_count'] > 0 || $errors['warning_count'] > 0 || $date === false) {
            $this->addErrors($key, 'datetime', [$format]);
        }
        return $this;
    }

    public function isValid():bool
    {
        return  empty($this->errors);
    }


    public function getErrors():array
    {

        return $this->errors;
    }

    private function addErrors(string $key, string $rule, array $attributes = [])
    {

        $this->errors[$key] = new ValidatorError($key, $rule, $attributes);
    }

    private function getValue(string $key)
    {

        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }

    public function exists(string $key, string $table, \PDO $pdo): self
    {
        $value = $this->getValue($key);
        $statement = $pdo->prepare("SELECT id FROM $table WHERE id = ?");
        $statement->execute([$value]);
        if ($statement->fetchColumn() === false) {
            $this->addErrors($key, 'exists', [$table]);
        }
        return $this;
    }


    public function unique(string $key, string $table, \PDO $pdo, ?int $exclude = null):self
    {
        $value = $this->getValue($key);

        $query = "SELECT id FROM $table WHERE $key = ?";
        $params = [$value];

        if ($exclude !== null) {
            $query .=" AND id != ?";
            $params[] = $exclude;
        }
        $statement = $pdo->prepare($query);
        $statement->execute($params);
        if ($statement->fetchColumn() !== false) {
            $this->addErrors($key, 'unique', [$value]);
        }
        return $this;
    }

    public function extension(string $key, array $extensions):self
    {
        /**
        *@var UploadedFileInterface $file
        */
        $file = $this->getValue($key);
        if (!is_null($file) && $file->getError() === UPLOAD_ERR_OK) {
            $type = $file->getClientMediaType();
            $extension = mb_strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
            $expectedType = self::MIME_TYPES[$extension] ?? null;
            if (!in_array($extension, $extensions) || $expectedType !== $type) {
                $this->addErrors($key, 'filetype', [join(',', $extensions)]);
            }
        }
        return $this;
    }

    public function uploaded(string $key):self
    {
        $file = $this->getValue($key);
        if ($file === null || $file->getError() !== UPLOAD_ERR_OK) {
            $this->addErrors($key, 'uploaded');
        }
        return $this;
    }

    /**
    * Vérifie si l'email est valide
    */
    public function email(string $key):self
    {
        $value = $this->getValue($key);
        if (filter_var($value,FILTER_VALIDATE_EMAIL) === false) 
        {
            $this->addErrors($key,'email');
        }
        return $this;
    }
}
