<?php

namespace App\Http\Controllers;

use App\Models\Ticket;

class TicketController extends Controller
{
    public function print(Ticket $ticket)
    {
        $ticket->load('ticketTemplate');
        return view('ticket.print', compact('ticket'));
    }
}
