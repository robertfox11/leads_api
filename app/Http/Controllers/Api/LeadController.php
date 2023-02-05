<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MailController;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LeadController extends Controller
{
    /**
     * Ordena por fecha de creacion del y filtrar por estado
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        $leads = Lead::orderBy('fecha_creacion', $request->order ?? 'desc')
            ->when($request->estatus, function ($query) use ($request) {
                return $query->where('estado_lead', $request->estatus);
            })
            ->get();
        return response()->json($leads);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Lead $lead
     * @return \Illuminate\Http\Response
     */
    public function show(Lead $lead){
        return $lead;
    }

    /**
     * Revisa los leads abiertos, los cierra los leads y envia correos electrónicos:
     *
     * @param \App\Models\Lead $lead
     * Envia App\Http\Controllers\MailController;
     */
    public function closeOldLeads(){
        $leads = Lead::where('estado_lead', 'abierto')
            ->where('fecha_creacion', '<', Carbon::now()->subMonths(6))
            ->get();
        foreach ($leads as $lead) {
            $lead->estado_lead = 'cerrado';
            $lead->fecha_cierre = Carbon::now();
            $lead->save();
            MailController::sendEmail($lead);
        }
    }
}
