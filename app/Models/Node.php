<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    use HasFactory;

    protected $casts = [
        'formula' => 'array'
    ];

    protected $appends = ['calculated','new_formula'];

    public function sceneries()
    {
        return $this->hasMany('App\Models\Scenery');
    }

    public function nodes()
    {
        return $this->hasMany('App\Models\Node');
    }

    public function node()
    {
        return $this->belongsTo('App\Models\Node');
    }

    public function getNewFormulaAttribute()
    {
        $new_formula = [];
        if (!$this->formula) {
            return null;
        }
        foreach ($this->formula as $key => $f) {
            if (gettype($f) == 'integer') {
                $n = Node::find($f);
                $new_formula[] = $n ? $n->name : 'NULL';
            }else{
                $new_formula[] = $f;
            }
        }

        return $new_formula;
    }

    private function recursiveCalculated($formula,$sc,$start)
    {
        $calculo = "";
        foreach ($formula as $key => $value) {

            if (gettype($value) == 'integer') {

                $node = Node::find($value);

                if (!$node) {
                    $calculo .= "0";
                }else{
                    if (count($node->sceneries)) {

                        foreach ($node->sceneries as $key => $n_sc) {

                            if ($n_sc->name == $sc) {

                                foreach ($n_sc->years as $k_year => $v_year) {

                                    if ($k_year == $start) {

                                        $calculo .= $v_year;

                                    }
                                }
                            }
                        }
                    }else{
                        $calculo .= $this->recursiveCalculated($node->formula,$sc,$start);
                    }
                }

            }else{
                $calculo .= $value;
            }
        }
        return $calculo;
    }

    public function getCalculatedAttribute()
    {

        $patron = "/([0-9]+)\\(([^)]+)\\)/";
        $reemplazo = "$1*($2)";

        $patron2 = "/\\)(?=\\()/";
        $reemplazo2 = ")*";

        if ($this->formula != null) {

            $sceneries = [];

            $proj = Project::find($this->project_id);

            foreach ($proj['sceneries'] as $key => $sc) {

                $years = [];
                $start = $proj->year_from;
                $count = 0;

                while($start <= $proj->year_to) {

                    $calculo = "";

                    foreach ($this->formula as $key => $value) {
                        $count++;

                        if (gettype($value) == 'integer') {

                            $node = Node::find($value);

                            if (!$node) {
                                $calculo .= "0";
                            }else{
                                if (count($node->sceneries)) {

                                    foreach ($node->sceneries as $key => $n_sc) {

                                        if ($n_sc->name == $sc) {

                                            foreach ($n_sc->years as $k_year => $v_year) {

                                                if ($k_year == $start) {

                                                    $calculo .= $v_year;

                                                }
                                            }
                                        }
                                    }
                                }else{
                                    $calculo .= '('.$this->recursiveCalculated($node->formula,$sc,$start).')';
                                }
                            }

                        }else{
                            $calculo .= $value;
                        }

                    }
                    $str = preg_replace($patron, $reemplazo, $calculo);

                    $st = 0;

                    while($st != $count) {
                        $str = preg_replace($patron, $reemplazo, $str);
                        $str = preg_replace($patron2, $reemplazo2, $str);
                        $st++;
                    }
                    $years[$start] = eval("return $str;");
                    // $years[$start] = $str;

                    $start++;
                }

                $sceneries[] = ["name" => $sc, "years" => $years];

            }


            return $sceneries;
        }
    }
}
