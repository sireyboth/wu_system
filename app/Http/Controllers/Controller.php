<?php
namespace App\Http\Controllers;

use function App\Helpers\no_data;
use Illuminate\Http\JsonResponse;

abstract class Controller
{
    protected $name = '';
    protected function not_found(string $id = '', string $message = 'not found'): JsonResponse
    {
        return no_data("{$this->name} {$message} with ID: {$id}", 404);
    }
}
