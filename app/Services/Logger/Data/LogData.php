<?php
namespace App\Services\Logger\Data;

use App\Models\User;
use Illuminate\Http\Request;

readonly class LogData
{
    public function __construct(
        public ?User      $user,
        public string     $entity,
        public string     $action,
        public \Throwable $exception,
        public Request    $request,
    ) {}
}
