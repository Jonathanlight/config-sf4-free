<?php

namespace App\Services;

class UploadType
{
    /**
     * @param $file
     * @param $generator
     * @param $pathname
     * @return bool
     */
    public function upload($file, $generator, $pathname)
    {
        if (isset($file['name'])) {
            $tmp_file = $file["tmp_name"];
            $target = $pathname.$generator;
            $extention_ = null;
            //Verification de la taille et le format du fichiers
            if (($file['size'] > 0)&& (($file["type"] == "image/jpeg") || ($file["type"] == "image/jpg") || ($file["type"] == "image/png") || ($file["type"] == "application/pdf"))) {
                // On Upload le fichier
                if (strstr($file["type"], 'jpg')) {
                    $extention_ = strstr($file["type"], 'jpg');
                }
                if (strstr($file["type"], 'png')) {
                    $extention_ = strstr($file["type"], 'png');
                }
                if (strstr($file["type"], 'jpeg')) {
                    $extention_ = strstr($file["type"], 'jpeg');
                }
                if (strstr($file["type"], 'pdf')) {
                    $extention_ = strstr($file["type"], 'pdf');
                }
                $confirm = move_uploaded_file($file["tmp_name"], $target.'.'.$extention_);
                if ($confirm) {
                    return true;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }

        return false;
    }

    /**
     * @param $type
     * @return bool|string
     */
    public function typeExtention($type)
    {
        if (strstr($type, 'png') || strstr($type, 'PNG')) {
            return 'png';
        }

        if (strstr($type, 'pdf') || strstr($type, 'PDF')) {
            return 'pdf';
        }

        if (strstr($type, 'jpg') || strstr($type, 'JPG')) {
            return 'jpg';
        }

        if (strstr($type, 'jpeg') || strstr($type, 'JPEG')) {
            return 'jpeg';
        }

        return false;
    }
}
