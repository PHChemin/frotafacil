<?php

namespace App\Services;

use Core\Database\ActiveRecord\Model;

class TruckBrandAvatar
{
    public function __construct(
        private Model $model
    ) {
    }

    public function path(): string
    {
        return $this->baseDir() . $this->model->name . ".png";
    }

    private function baseDir(): string
    {
        return "/assets/images/{$this->model::table()}/";
    }
}
