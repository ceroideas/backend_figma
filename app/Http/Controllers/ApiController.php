<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Node;
use App\Models\Scenery;
use App\Models\Simulation;
use DB;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;

class ApiController extends Controller
{
    //
    public function migrar()
    {
        Schema::table('nodes', function(Blueprint $table) {
            //
            $table->integer('default_year')->nullable();
            $table->string('line_color')->nullable();
            $table->integer('default_growth')->nullable();
            $table->integer('default_growth_percentage')->nullable();
        });

        return;
        Schema::table('nodes', function(Blueprint $table) {
            //
            $table->json('new_formula')->nullable();
        });

        return;
        Schema::table('projects', function(Blueprint $table) {
            //
            $table->integer('default_year')->nullable();
            $table->string('line_color')->nullable();
            $table->integer('default_growth')->nullable();
            $table->integer('default_growth_percentage')->nullable();
        });

        return;
        Schema::table('projects', function(Blueprint $table) {
            //
            $table->string('thumb')->nullable();
        });

        return;
        Schema::table('nodes', function(Blueprint $table) {
            //
            $table->integer('hidden_table')->nullable();
            $table->integer('hidden_node')->nullable();
        });
        Schema::dropIfExists('simulations');
        Schema::create('simulations', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('steps')->nullable();
            $table->string('color')->nullable();

            $table->string('nodes')->nullable();
            $table->longText('samples')->nullable();
            $table->string('simulation')->nullable();

            $table->longText('csvData')->nullable();

            $table->timestamps();
        });
    }

    public function getProjects()
    {
        return Project::orderBy('id','desc')->get();
    }

    public function getProject($id)
    {
        return Project::with('nodes','nodes.sceneries')->where('id',$id)->first();
    }

    public function getNode($id)
    {
        return Node::with('sceneries')->where('id',$id)->first();
    }

    public function getScenery($id)
    {
        return Scenery::find($id);
    }

    public function saveProject(Request $r)
    {
        $p = new Project;
        $p->user_id = 1;
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

            $start = $p->year_from;

            while($start <= $p->year_to)
            {
                $years[$start] = $default;
                $start++;
            }

            $s = new Scenery;
            $s->node_id = $n->id;
            $s->name = $sc;
            $s->years = $years;
            $s->status = 1;
            $s->save();
        }

        return redirect('api/getNode/'.$n->id);
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
        if ($a==0) {
            $sceneries[] = $r->name;
            $p->sceneries = $sceneries;

            $p->save();
        }

        $nodes = Node::where('project_id',$p->id)/*->where('type',1)*/->get();

        foreach ($nodes as $key => $n) {
            
            $scen = Scenery::where(['node_id' => $n->id, 'name' => $r->name])->count();

            if ($scen == 0) {

                $years = [];

                $default = 0;

                if ($n->unite) {
                    $default = $n->unite;
                }

                $start = $p->year_from;

                while($start <= $p->year_to)
                {
                    $years[$start] = $default;
                    $start++;
                }

                $s = Scenery::where('name',$r->name)->where('node_id',$n->id)->first();

                if (!$s) {
                    $s = new Scenery;
                }
                $s->node_id = $n->id;
                $s->name = $r->name;
                $s->years = $n->id == $r->node_id ? $r->years : $years;
                $s->status = 1;
                $s->save();
            }
        }
    }

    public function saveSceneryNoPropagation(Request $request)
    {
        $iterados = [];

        foreach ($request->data as $key => $r) {
        
            $n = Node::find($r['node_id']);
            $p = Project::find($n->project_id);

            $sceneries = $p->sceneries;
            
            $a = 0;

            foreach ($sceneries as $key => $sc) {
                if ($sc == $r['name']) {
                    $a++;
                }
            }
            if ($a==0) {
                $sceneries[] = $r['name'];
                $p->sceneries = $sceneries;

                $p->save();
            }

            // $nodes = Node::where('id',$r['node_id'])->get();
            $nodes = Node::where('project_id',$p->id)->whereIn('type',[1,2])->get();

            foreach ($nodes as $key => $n) {
                
                $scen = Scenery::where(['node_id' => $n->id, 'name' => $r['name']])->count();

                if ($scen == 0) {

                    $years = [];

                    $default = 0;

                    if ($n->unite) {
                        $default = $n->unite;
                    }

                    $start = $p->year_from;

                    while($start <= $p->year_to)
                    {
                        $years[$start] = $default;
                        $start++;
                    }

                    if ($n->id == $r['node_id']) {
                        $iterados[] = $r['node_id'];
                    }

                    $s = new Scenery;
                    $s->node_id = $n->id;
                    $s->name = $r['name'];
                    $s->years = $n->id == $r['node_id'] ? $r['years'] : $years;
                    $s->status = 1;
                    $s->save();
                }else{
                    if (!in_array($n->id, $iterados)) {
                        $iterados[] = $r['node_id'];
                        $s = Scenery::where('name',$r['name'])->where('node_id',$n->id)->first();
                        $s->node_id = $n->id;
                        $s->name = $r['name'];
                        $s->years = $r['years'];
                        $s->status = 1;
                        $s->save();
                    }
                }
            }

        }
    }

    public function updateProject($id, Request $r)
    {
        $p = Project::find($id);

        if (!file_exists(public_path() . '/projects/')) {
            mkdir(public_path() . '/projects/', 0777, true);
        }

        $base64_image = $r->input('thumb'); 
        $exploded = explode(',', $base64_image);

        $decoded_image = base64_decode($exploded[1]);
        $name = 'thumb-'.$p->id.'.jpg';
        $path = public_path() . '/projects/'.$name; 
        
        file_put_contents($path, $decoded_image);

        $p->thumb = $name;
            
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
        $s->status = isset($r->status) ? $r->status : $s->status;
        $s->save();
    }

    public function deleteProject($id)
    {
        Project::find($id)->delete();
        $nodes = Node::where('project_id',$id);
        foreach ($nodes->get() as $key => $n) {
            Scenery::where('node_id',$n->id)->delete();
        }
        $nodes->delete();
    }

    public function deleteNode($id)
    {
        Node::find($id)->delete();
        Scenery::where('node_id',$id)->delete();

        function deleteNodes($n)
        {
            foreach ($n->nodes as $key => $node) {
                if ($node->nodes) {
                    deleteNodes($node);
                }
                $node->delete();
                Scenery::where('node_id',$node->id)->delete();
            }
        }

        $nodes = Node::where('node_id','!=',null)->doesntHave('node')->get();

        foreach ($nodes as $key => $n) {
            if ($n->nodes) {
                deleteNodes($n);
            }
            $n->delete();
            Scenery::where('node_id',$n->id)->delete();
        }
    }

    public function deleteScenery($id)
    {
        $sc = Scenery::find($id);
        $n = Node::find($sc->node_id);
        $p = Project::find($n->project_id);

        // falta
    }

    public function savePosition(Request $r,$id)
    {
        $p = Project::find($id);
        $p->position = $r->position;
        $p->save();
    }

    public function saveZoom(Request $r,$id)
    {
        $p = Project::find($id);
        $p->zoom = $r->zoom;
        $p->save();
    }

    public function saveUnite(Request $r,$id)
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
                while($start <= $p->year_to)
                {
                    $years[$start] = $n->unite;
                    $start++;
                }
                $value->years = $years;
                $value->save();
            }
        }
    }

    public function getSimulations($id)
    {
        return Simulation::where('project_id',$id)->get();
    }

    public function getSimulation($id)
    {
        return Simulation::find($id);
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
        $name = 'simulation-'.$s->id.'.jpg';
        $path = public_path() . '/simulations/'.$name; 
        
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
        $s->save();

        if (!file_exists(public_path() . '/simulations/')) {
            mkdir(public_path() . '/simulations/', 0777, true);
        }

        if ($r->simulation) {
            $base64_image = $r->input('simulation'); 
            $exploded = explode(',', $base64_image);

            $decoded_image = base64_decode($exploded[1]);
            $name = 'simulation-'.$s->id.'.jpg';
            $path = public_path() . '/simulations/'.$name; 
            
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

    public function uploadProject($id,Request $request)
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
}
