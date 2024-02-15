<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Node;
use App\Models\Scenery;
use DB;

class ApiController extends Controller
{
    //
    public function migrar()
    {        
        return Project::with('nodes','nodes.sceneries')->first();
    }

    public function getProjects()
    {
        return Project::get();
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
        $p->status = 1;
        $p->save();

        return redirect('api/getProjects');
    }

    public function saveNode(Request $r)
    {
        $n = new Node;
        $n->project_id = $r->project_id;
        $n->node_id = $r->node_id;
        $n->tier = $r->tier;
        $n->name = $r->name;
        $n->description = $r->description;
        $n->type = $r->type;
        $n->distribution_shape = $r->distribution_shape;
        $n->formula = $r->formula;
        $n->status = 1;
        $n->save();

        $p = Project::find($r->project_id);

        foreach ($p->sceneries as $key => $sc) {

            $years = [];

            $start = $p->year_from;

            while($start <= $p->year_to)
            {
                $years[$start] = 0;
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

        $years = [];

        while($p->year_from <= $p->year_to)
        {
            $years[$p->year_from] = 0;
            $p->year_from++;
        }

        foreach ($nodes as $key => $n) {
            
            $scen = Scenery::where(['node_id' => $n->id, 'name' => $r->name])->count();

            if ($scen == 0) {
                $s = new Scenery;
                $s->node_id = $n->id;
                $s->name = $r->name;
                $s->years = $n->id == $r->node_id ? $r->years : $years;
                $s->status = 1;
                $s->save();
            }
        }
    }

    public function updateProject($id, Request $r)
    {
        $p = Project::find($id);
        $p->name = $r->name;
        // $p->year_from = $r->year_from;
        // $p->year_to = $r->year_to;
        // $p->sceneries = $r->sceneries;
        $p->status = $r->status ? $r->status : $p->status;
        $p->save();
    }

    public function updateNode($id, Request $r)
    {
        $n = Node::find($id);
        $n->name = $r->name;
        $n->description = $r->description;
        $n->type = $r->type;
        $n->distribution_shape = $r->distribution_shape;
        $n->formula = $r->formula;
        $n->status = $r->status ? $r->status : $n->status;
        $n->save();
    }

    public function updateScenery($id, Request $r)
    {
        $s = Scenery::find($id);
        // $s->name = $r->name;
        $s->years = $r->years;
        $s->status = $r->status ? $r->status : $s->status;
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
}
