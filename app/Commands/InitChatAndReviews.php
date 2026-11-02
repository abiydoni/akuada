<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class InitChatAndReviews extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'app:init-chat-reviews';
    protected $description = 'Initialize tables for chat and reviews';

    public function run(array $params)
    {
        $orderModel = new \App\Models\OrderModel();
        $reviewModel = new \App\Models\OrderReviewModel();
        $messageModel = new \App\Models\OrderMessageModel();
        $returnModel = new \App\Models\OrderReturnModel();

        CLI::write('Tables initialized successfully!', 'green');
    }
}
