<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{

    public function __construct(
        private SluggerInterface $slugger,
        private string $targetDirectory 
    ) {}

    public function upload(UploadedFile $file): ?string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $newFilename);
            return $newFilename; 
        } catch (FileException $e) {
            return null; 
        }
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}