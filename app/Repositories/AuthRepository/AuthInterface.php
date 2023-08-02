<?php
namespace App\Repositories\AuthRepository;


interface AuthInterface
{
  /***************
   * request create User
   ***************/

   public function createUser($data);
}