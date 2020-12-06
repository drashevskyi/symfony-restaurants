<?php

namespace App\Tests\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\FileService;

class FileServiceTest extends KernelTestCase
{   
    public function testUploadRemove()
    {
        //getting file service
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $container = self::$container;
        $fileService = $container->get(FileService::class);
        //creating image 'php rules'
        $fileName = 'test.jpg';
        $data = 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
           . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
           . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
           . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';
        $data = base64_decode($data);
        $imgRaw = imagecreatefromstring($data);
        
        if ($imgRaw !== false) {
            imagejpeg($imgRaw, 'public/uploads/test.jpg', 100);
            imagedestroy($imgRaw);
            //test upload
            $file = new UploadedFile( 'public/uploads/test.jpg', 'test.jpg', 'image/jpeg', null, true, true);
            $newFileName = $fileService->upload($file);
            $this->assertTrue(file_exists('public/uploads/images/'.$newFileName));
            //test remove
            $fileService->remove($newFileName);
            $this->assertFalse(file_exists('public/uploads/images/'.$newFileName));
        }
    }

}
