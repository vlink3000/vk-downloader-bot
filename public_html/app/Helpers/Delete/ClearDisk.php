<?php

declare(strict_types=1);

namespace App\Helpers\Delete;

class ClearDisk {

    public function rmPhotos(string $path): bool
    {

        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? removeDirectory($file) : unlink($file);
        }
        rmdir($path);

        return true;
    }

    public function rmArchive()
    {

    }
}