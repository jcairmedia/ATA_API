<?php

namespace App\Utils;

use Illuminate\Http\Request;

class FileUtils
{
    public function __construct(Request $file)
    {
        $this->file = $file;
    }

    public function read(string $nameTagFile)
    {
        try {
            //code...

            if (!$this->file->file($nameTagFile)->isValid()) {
                throw new \Exception('Error en la carga del archivo: '.$this->file->file($nameTagFile), 400);
            }
            $namefile = $this->file->file($nameTagFile)->getClientOriginalName();
            $extension = pathinfo($namefile, PATHINFO_EXTENSION); //get extension
            if (false === (array_search(
                $extension,
                [
                    'jpg',
                    'jpeg',
                    'png',
                    'pdf',
                ],
                true
            ))
            ) {
                throw new \Exception('Invalid file format.');
            }

            return [$namefile, $extension];
        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());
        }
    }

    public function save($path, $newName)
    {
        $path = $this->file->file->storeAs(
            $path, $newName
        );

        return $path;
    }
}
