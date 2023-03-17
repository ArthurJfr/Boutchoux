<?php
namespace Core\Helper;

/**
 * @author Petyth Prince Belvie
 */
class DownloadFile
{
    /**
     * @param string $filePath
     * @param string $fileType
     */

    public function downloadFile(string $filePath, string $fileType = 'pdf') {

        if (!file_exists($filePath)) {
            $message = 'le fichier fourni n\'existe pas';
            return $message;
        }

        $fileData = file_get_contents($filePath);

        switch ($fileType) {
            case 'pdf':
                header('Content-Type: application/pdf');
                break;
            case 'svg':
                header('Content-Type: image/svg+xml');
                break;
            case 'jpeg':
            case 'jpg':
                header('Content-Type: image/jpeg');
                break;
            case 'png':
                header('Content-Type: image/png');
                break;
            default:
                header('Content-Type: application/octet-stream');
                break;
        }

        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));
        echo $fileData;
        exit;
    }

}