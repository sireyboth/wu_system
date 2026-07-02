<?php
namespace App\Helpers;

trait Generic
{
    protected function not_found(string $id = '', string $message = 'not found')
    {
        return no_data("{$this->name} {$message} with ID: {$id}", 404);
    }
}
