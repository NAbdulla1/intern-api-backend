<?php


namespace Controller;

use Utils\OtherResponse;
use Utils\ResponseCodes;

class ImageController
{
    public function delete($content)
    {
        if (empty($content) || empty($content['url']) || !filter_var($content['url'], FILTER_VALIDATE_URL)) {
            OtherResponse::send(ResponseCodes::HTTP_BAD_REQUEST, "Invalid Url");
            return;
        }
        $path = $this->getAbsolutePath($content['url']);
        if (file_exists($path)) {
            if (unlink($path)) OtherResponse::send(ResponseCodes::HTTP_NO_CONTENT, "Deleted successfully");
            else OtherResponse::send(ResponseCodes::HTTP_BAD_REQUEST, "");
        } else OtherResponse::send(ResponseCodes::HTTP_NOT_FOUND, "Image Not Found");
    }

    private function getAbsolutePath($url): string
    {
        $parts = explode("/", $url);
        $parts = explode("\\", array_pop($parts));
        return __DIR__ . "/../../images/" . array_pop($parts);
    }

    public function upload($image)
    {
        if ($this->isInvalidImage($image)) return;
        [$urlPath, $uploadPath] = $this->getPaths($image);
        if (move_uploaded_file($image['tmp_name'], $uploadPath)) {
            http_response_code(ResponseCodes::HTTP_CREATED);
            echo json_encode(["message" => "image uploaded", "path" => $urlPath]);
        } else OtherResponse::send(ResponseCodes::HTTP_INTERNAL_SERVER_ERROR, "image upload failed");
    }

    private function isInvalidImage($image): bool
    {
        if ($image['size'] <= 0 || !$this->isImageType($image['type']) || $image['error'] != 0) {
            OtherResponse::send(ResponseCodes::HTTP_BAD_REQUEST, "Invalid Image");
            return true;
        }
        return false;
    }

    private function isImageType($type): bool
    {
        $allowedImageTypes = ["image/png", "image/jpg", "image/jpeg", "image/webp"];
        foreach ($allowedImageTypes as $imageType)
            if (strcasecmp($imageType, $type) == 0) return true;
        return false;
    }

    private function getPaths($image): array
    {
        $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
        $name = time() . "" . rand() . ".$extension";
        return ["http://intern.local/images/" . $name, __DIR__ . "/../../images/" . $name];
    }
}