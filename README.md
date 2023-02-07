<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">

</p>

## Creación de una API de leads y envio de email y consumo con React

En este proyecto se creará una API de leads utilizando Laravel y se consumirá en una aplicación de React.

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Para ejecutar la aplicacion
    
    composer global require laravel/installer
    php artisan serve

## Creación de la API de leads con Laravel

composer create-project --prefer-dist laravel/name-project
## Crea un controlador y modelo  para manejar las solicitudes de la API de leads:

    php artisan make:controller  Api/LeadController --api --model=Lead
    php artisan make:

### Modelo Lead
    class Lead extends Model
    {
        use HasFactory;
        protected $fillable=[
        'titulo',
        'estado_lead',
        'fecha_creacion',
        'fecha_cierre'
        ];
    }

## Datos de prueba
     $arrays = range(0, 20);
        $estados = ['abierto', 'rechazado', 'aceptado', 'cerrado'];
        foreach ($estados as $es) {
            foreach ($arrays as $valor) {
                if ($es == 'abierto') {
                    if ($valor <= 10) {
                        DB::table('leads')->insert([
                            'titulo' => "Title  $valor " . $es,
                            'estado_lead' => $es,
                            'fecha_creacion' => date("2022-m-d H:i:s"),
                            'fecha_cierre' => null
                        ]);
                    }
                } else {
                    DB::table('leads')->insert([
                        'titulo' => "Title  $valor " .$es,
                        'estado_lead' => $es,
                        'fecha_creacion' => date("Y-m-d H:i:s"),
                        'fecha_cierre' => null
                    ]);
                }
            }
        }

## Caso 1 leads:
#### LeadController Modifica el controlador para incluir una acción para devolver los leads ordenados y filtrados  por estado

    class LeadController extends Controller{
        public function index(Request $request)
        {
            $leads = Lead::orderBy('fecha_creacion', $request->order ?? 'desc')
                ->when($request->estatus, function ($query) use ($request) {
                    return $query->where('estatus', $request->estatus);
                })
                ->get();
    
            return response()->json($leads);
        }
    }

## Caso2 Lead Controller obtiene el id de leads
    class LeadController extends Controller{
        public function show(Lead $lead)
        {
        return $lead;
        }
    }
    
##  Caso 3: Cierre de leads según fecha
#### Nuestra API revisará todos los leads abiertos que tengan más de 6 meses desde su creación, los cerrará y 
#### enviará una notificación a un email
#### con la siguiente informacion
#### REMITENTE: NUESTRONOMBRE@pruebawe-accom.com
#### ASUNTO: ID LEAD - TITULO LEAD
#### DESCRIPCIÓN: El lead ID_LEAD con título TITULO_LEAD ha sido cerrado.



#### 3.1 Configuracion de variables de entorno y mail.php

    MAIL_MAILER=smtp
    MAIL_HOST=smtp.gmail.com
    MAIL_PORT=587
    MAIL_USERNAME=
    MAIL_PASSWORD=
    MAIL_ENCRYPTION=TLS
    MAIL_FROM_ADDRESS=
    MAIL_FROM_NAME="${APP_NAME}"
    
## configuracion mail.php en la carpeta config
     'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'auth_mode' => null,
            'stream' => [
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ],
        ],
    ]
#### 3.2  Creamos una clase de correo electronico utilizando
    php artisan make:mail LeadClosed
    
#### Agregamos estos valores en la clase class LeadClosed extends Mailable

    class LeadClosed extends Mailable{
        public $lead;
        public function __construct($lead){
            $this->lead = $lead;
        }
        public function build(){
            return $this->subject('Lead Cerrado')
                ->view('emails.lead_closed');
        }
    }


#### creamos una vista para poder llegar los datos
    <p>El lead "0000{{ $lead->id }}" ha sido cerrado por haber estado abierto por más de 6 meses.</p>
    <p>Fecha de creación: {{ $lead->fecha_creacion }}</p>
    <p>Fecha de cierre: {{ $lead->fecha_cierre }}</p>

#### utilizamos la clase LeadClosed para el envio de email
    class MailController extends Controller {
        public function sendEmail($lead){
        Mail::to('11rsahome@gmail.com')->send(new LeadClosed($lead));
        }
    }


#### En el Controlador LeadController leads abiertos que tengan más de 6 meses desde su creación, los cerrará y
#### enviará una notificación a un email  

     public function closeOldLeads()
    {
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

## Ruta Api

    Route::get('/leads', [LeadApiController::class, 'index']);
    Route::get('leads/{lead}', [LeadApiController::class, 'show']);
    Route::get('closeOldLeads',[LeadApiController::class, 'closeOldLeads']);


## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
