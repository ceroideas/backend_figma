<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;
class Scenery extends Model
{
    use HasFactory;

    protected $casts = [
        'years' => 'array',
        'dynamic_years' => 'array'
    ];

    // protected $appends = ['years_test'];


    // public function getYearsTestAttribute()
    // {
    //     $node = Node::find($this->node_id);
    //     $proj = Project::find($node->project_id);
    //     $patron = "/([0-9]+)\\(([^)]+)\\)/";
    //     $reemplazo = "$1*($2)";
    //     $esc_years = $this->years;

    //     $patron2 = "/\\)(?=\\()/";
    //     $reemplazo2 = ")*";

    //     if ($this->dynamic_years != null) {

    //         foreach($this->dynamic_years as $key => $dy){
    //             if (!isset($dy->year) || !isset($dy->formula)) {
                    
    //                 continue; 
    //             }
    //             $year = $dy->year;
    //             $calculo = "";
    //             $count = 0;
    //             foreach ($dy->formula as $key => $value) {
    //                 $count++;

    //                 if (gettype($value) == 'integer') {

    //                    switch ($value) {
    //                     case 1:
    //                         $calculo .= $node->default_growth_percentage;
    //                         break;
                        
    //                     case 2:
    //                         $calculo .= $proj->default_growth_percentage;
    //                         break;

    //                     case 3:
    //                         $calculo .= $this->years[$proj->default_year];
    //                         break;
    //                    }

                        
    //                 }else{
    //                     $calculo .= $value;
    //                 }

    //             }
    //             $str = preg_replace($patron, $reemplazo, $calculo);

    //             $st = 0;
    //             while($st != $count) {
    //                 $str = preg_replace($patron, $reemplazo, $str);
    //                 $str = preg_replace($patron2, $reemplazo2, $str);
    //                 $st++;
    //             }
    //             $pattern = "/(\/null)|(\*null)/";
    //             $replacement = "*1";
    //             $str = preg_replace($pattern, $replacement, $str);
    //             $valor = $this->evaluarExpresion($str);

    //             $esc_years[$year] = $valor;
    //         }
         
    //     }
        
        
    //     return  $esc_years;
    // }


    public function getYearsAttribute($value)
    {
        $node = Node::find($this->node_id);
        if (!$node) {
            throw new \Exception("Node not found");
        }
    
        $proj = Project::find($node->project_id);
        if (!$proj) {
            throw new \Exception("Project not found");
        }
        $sce_years = json_decode($value, true);
    
        if ($this->dynamic_years != null) {
            foreach ($this->dynamic_years as $dy) {
                
                if (!isset($dy['year']) || !isset($dy['formula'])) {
                    continue; 
                }
    
                $year = $dy['year'];  
                $calculo = "";
                $count = count($dy['formula']); 
                $is_empty = false; 

                if (!empty($dy['formula'])) {
                    foreach ($dy['formula'] as $item) {
                        $formulaPart = $item['formula'];  
                        if (empty($formulaPart) && is_array($formulaPart)) {
                            $is_empty = true;
                            break;  
                        }
                        $calculo = $this->processFormula($formulaPart, $node, $proj, $calculo, $sce_years, $year);
                    }
        
                   
                    $calculo = $this->applyRegularExpressions($calculo);
        
                  
                    $valor = $this->evaluarExpresion($calculo);
                    $sce_years[$year] = $is_empty ? $sce_years[$year] : $valor; 
                }
    
 
            }
        }
    
        return $sce_years;
    }

    private function processFormula($formulaParts, $node, $proj, $calculo, $esc_years, $year)
    {
        foreach ($formulaParts as $value) {
           
            if (is_int($value)) {
                switch ($value) {
                    case 1:
                        $calculo .= $node->default_growth_percentage; 
                        break;
    
                    case 2:
                        $calculo .= $proj->default_growth_percentage;  
                        break;
    
                    case 3:
                        $calculo .= $esc_years[$proj->default_year]; 
                        break;

                    case 4:
                        $temporal_year = $year - 1;
                        if($proj->default_year >= $temporal_year) {
                            break;
                        }
                    $calculo .= $esc_years[$temporal_year]; 
                    break;  
                }
            } else {
               
                $calculo .= $value;
            }
        }
    
        return $calculo;
    }

    private function applyRegularExpressions($calculo)
{
    $patron = "/([0-9]+)\\(([^)]+)\\)/";
    $reemplazo = "$1*($2)";
    $patron2 = "/\\)(?=\\()/";
    $reemplazo2 = ")*";


    $str = preg_replace($patron, $reemplazo, $calculo);
    $st = 0;
    $count = 10; 

   
    while ($st < $count) {
        $str = preg_replace($patron, $reemplazo, $str);
        $str = preg_replace($patron2, $reemplazo2, $str);
        $st++;
    }

    
    $pattern = "/(\/null)|(\*null)/";
    $replacement = "*1";
    $str = preg_replace($pattern, $replacement, $str);

    return $str;
}

public function convertirExponente($expresion) {
    return preg_replace('/(\d+)\s*\^\s*(\d+)/', 'pow($1,$2)', $expresion);

}

function evaluarExpresion($expresion) { 
    // $expresion = preg_replace_callback('/(\d+)\s*\^\s*(\d+)/', function($matches) { return 'pow(' . $matches[1] . ',' . $matches[2] . ')'; }, $expresion);  
    // try { 
    //     $resultado = eval('return ' . $expresion . ';'); 
    //     return $resultado; 
    // } 
    // catch (ParseError $e) { 
    //     return 0; 
    // } 
    return 42;
}
}
