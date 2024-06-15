<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;


class Node extends Model
{
    use HasFactory;

    protected $casts = [
        'formula' => 'array',
        'distribution_shape' => 'array'
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

    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    public function getUniteAttribute($val)
    {
        if (strpos($val, '%')) {
            return intval($val) / 100;
        }
        return $val;
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
                    if (count($node->sceneries) && $node->type == 1) {

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
        if (!$calculo) {
            return 0;
        }
        return $calculo;
    }

    private function evaluarExpresion($expresion) {
        $language = new ExpressionLanguage();
        try {
            return $language->evaluate($expresion);
        } catch (SyntaxError $e) {
            return 0;
        }
    }

    public function getCalculatedAttribute()
    {

        $patron = "/([0-9]+)\\(([^)]+)\\)/";
        $reemplazo = "$1*($2)";

        $patron2 = "/\\)(?=\\()/";
        $reemplazo2 = ")*";
        
        $sceneries = [];


        if ($this->formula != null && $this->type == 2) {

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
                                if (count($node->sceneries) && $node->type == 1) {

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
                    $valor = $this->evaluarExpresion($str);
                    if ($valor !== null) {
                        $years[$start] = $valor;
                    } else {
                        $years[$start] = 0;
                    }

                    // $years[$start] = eval("return number_format($str,2);");
                    // $years[$start] = $str;

                    $start++;
                }

                $sceneries[] = ["name" => $sc, "years" => $years];

            }


            return $sceneries;
        }

        return $this->project->clean_sceneries;
    }
}
