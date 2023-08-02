<?php
namespace App\Repositories\LoanRepository;


interface LoanInterface
{
  /***************
   * request loan
   ***************/

   public function createLoanRequest($data, $uid);

   public function getRequestedLoans($uid);


  /****************
   *  loan 
   ****************/

   public function createLoan($data);

   public function respondOffer($loan_id, $user_respond);

   public function isOfferedPerson($user_id, $loan_id);

   public function details($loan_id);

   public function getLoansByUserId($user_id);

   public function isLoanAccepted($loan_id);

}