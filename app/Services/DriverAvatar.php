<?php

namespace App\Services;

use Core\Constants\Constants;
use Core\Database\ActiveRecord\Model;

class DriverAvatar
{
    /** @var array<string, mixed> $image */
    private array $image;
    private array $validationParams;

    public function __construct(
        private Model $model,
        $validationParams
    ) {
        $this->validationParams = $validationParams;
    }

    public function path(): string
    {
        if ($this->model->avatar_name) {
            $hash = md5_file($this->getAbsuleSavedFilePath());
            return $this->baseDir() . $this->model->avatar_name . "?" . $hash;
        }

        return "/assets/images/defaults/avatar.png";
    }

    /**
     * @param array<string, mixed> $image
     */
    public function update(array $image): bool
    {
        $this->image = $image;

        if ($this->validationParams['extension']) {
            $this->extensionValidation();
        }

        if ($this->validationParams['size']) {
            $this->sizeValidation();
        }

        if ($this->model->hasErrors()) {
            return false;
        }

        if (!empty($this->getTmpFilePath())) {
            $this->removeOldImage();
            $this->model->update(['avatar_name' => $this->getFileName()]);
            move_uploaded_file($this->getTmpFilePath(), $this->getAbsoluteFilePath());
        }

        return true;
    }

    private function getTmpFilePath(): string
    {
        return $this->image['tmp_name'];
    }

    private function removeOldImage(): void
    {
        if ($this->model->avatar_name) {
            $path = Constants::rootPath()->join('public' . $this->baseDir())->join($this->model->avatar_name);
            unlink($path);
        }
    }

    private function getFileName(): string
    {
        $file_name_splitted  = explode('.', $this->image['name']);
        $file_extension = end($file_name_splitted);
        return 'avatar.' . $file_extension;
    }

    private function getAbsoluteFilePath(): string
    {
        return $this->storeDir() . $this->getFileName();
    }

    private function getAbsuleSavedFilePath(): string
    {
        return Constants::rootPath()->join('public' . $this->baseDir())->join($this->model->avatar_name);
    }

    private function baseDir(): string
    {
        return "/assets/uploads/{$this->model::table()}/{$this->model->id}/";
    }

    private function storeDir(): string
    {
        $path = Constants::rootPath()->join('public' . $this->baseDir());
        if (!is_dir($path)) {
            mkdir(directory: $path, recursive: true);
        }

        return $path;
    }

    private function extensionValidation(): bool
    {
        $supportedExtensions = $this->validationParams['extension'];

        $file_name_splitted  = explode('.', $this->image['name']);
        $file_extension = end($file_name_splitted);

        if (!in_array($file_extension, $supportedExtensions)) {
            $this->model->addError('avatar_name', 'Formato de imagem não suportado!');
            return false;
        }
        return true;
    }

    private function sizeValidation(): bool
    {
        $maxSize = $this->validationParams['size']; // 2MB

        if ($this->image['size'] > $maxSize) {
            $this->model->addError('avatar_name', 'O arquivo deve ter no máximo 2MB!');
            return false;
        }

        return true;
    }
}
