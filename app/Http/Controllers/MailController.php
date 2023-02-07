<?php

namespace App\Http\Controllers;

use App\Mail\LeadClosed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendEmail($lead){
        Mail::to(env('MAIL_FROM_ADDRESS'))->send(new LeadClosed($lead));
    }
}
