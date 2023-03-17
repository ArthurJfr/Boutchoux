<?php
namespace App\Service;

class Validation
{
    protected $errors = array();

    public function IsValid($errors)
    {
        foreach ($errors as $key => $value) {
            if(!empty($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * emailValid
     * @param email $email
     * @return string $error
     */

    public function emailValid($email)
    {
        $error = '';
        if(empty($email) || (filter_var($email, FILTER_VALIDATE_EMAIL)) === false) {
            $error = 'Adresse email invalide.';
        }
        return $error;
    }

    /**
     * textValid
     * @param POST $text string
     * @param title $title string
     * @param min $min int
     * @param max $max int
     * @param empty $empty bool
     * @return string $error
     */

    public function textValid($text, $title, $min = 3,  $max = 50, $empty = true)
    {

        $error = '';
        if(!empty($text)) {
            $strtext = strlen($text);
            if($strtext > $max) {
                $error = 'Votre ' . $title . ' est trop long.';
            } elseif($strtext < $min) {
                $error = 'Votre ' . $title . ' est trop court.';
            }
        } else {
            if($empty) {
                $error = 'Veuillez renseigner un ' . $title . '.';
            }
        }
        return $error;

    }

    /**
     * @author Moussi Mohamed-Amir
     * @param $file
     * @param array $allowedMimeTypes
     * @param int $maxSize
     * @return string
     */
    public function fileValid($file, array $allowedMimeTypes = array('image/png','image/jpeg','image/jpg'),int $maxSize = 2000000)
    {
        $error = '';
        if($file['error'] > 0) {
            if($file['error'] != 4) {
                $error = 'Error: ' . $file['error'];
            } else {
                $error = 'Veuillez renseigner une image';
            }
        } else {
            $file_name = $file['name'];
            $file_size = $file['size'];
            $file_tmp  = $file['tmp_name'];
            $file_type = $file['type'];

            if($file_size > $maxSize || filesize($file_tmp) > $maxSize) {
                $error = 'Votre fichier est trop gros (max '. ($maxSize / 1000000) .'mo).';
            } else {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file_tmp);
                if(!in_array($mime, $allowedMimeTypes)) {
                    $error = 'Veuillez télécharger une image du type ' . implode(' ou ', $allowedMimeTypes);
                }
            }
        }
        return $error;
    }

    /**
     * @author Moussi Mohamed-Amir
     * @param string $url
     * @return string
     */
    public function urlValid(string $url) : string
    {
        $error = '';
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $error = 'URL invalide. Ex : https://www.google.com/';
        }
        return $error;
    }

}
