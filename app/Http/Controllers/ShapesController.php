<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Project;
use App\Models\Node;

class ShapesController extends Controller
{
    public function recursiveCalculate($node)
    {
        for ($i = 0; $i < count($node->formula); $i++) {
            $nodeId = $node->formula[$i];
            if (gettype($nodeId) == 'integer') {

                $node = Node::find($nodeId);
                $csvData[$j] = array_merge($csvData[$j], array("id" => $this->simulationId));
                if ($node->type == 1) {
                    if (!$node->isActive || $node->isActive == false) {
                      $value =
                        $node->unite == null || $node->unite == ""
                          ? '0'
                          : $node->unite;
                      $formula[] = $value;

                      $index = array_search($node->name, array_column($valoresPorNodo, "name"));

                      if ($index !== false) {
                        $aux = $valoresPorNodo[$index]
                      }else{
                        $aux = false;
                      }
                      // $csvData[$j] = array_merge($csvData[$j], array([$node->name] => $value));
                      $csvData[$j] = array_merge($csvData[$j], array($node->name => $value));
                      if (!$aux) {
                        $valoresPorNodo[] = ["name" => $node->name, "values" => [$value]];
                      } else {
                        $values = $aux['values'];
                        $values[] = $value;
                        $aux['values'] = $values;
                      }
                    } else {
                        switch ($node->distribution_shape[0]['name']) {
                            case 'Uniforme':
                                $randomNumber = $this->uniformOperation(
                                    $node->distribution_shape[0]['min'],
                                    $node->distribution_shape[0]['max'],
                                    $simulationNumber
                                  );
                                  $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                  if ($index !== false) {
                                    $aux = $valoresPorNodo[$index]
                                  }else{
                                    $aux = false;
                                  }
                                  if (!$aux) {
                                    $valoresPorNodo[] = [
                                      "name"=> $node->name,
                                      "values" => [$randomNumber],
                                    ];
                                  } else {
                                    $values = $aux['values'];
                                    $values[] = $randomNumber;
                                    $aux['values'] = $values;
                                  }
                                  $formula[] = '(' . $randomNumber . ')';
                                  $csvData[$j] = array_merge($csvData[$j], array($node->name => $randomNumber));
                                break;
                            case 'Triangular':
                                $triangularNumber = $this->triangularOperation(
                                    $node->distribution_shape[0]['min'],
                                    $node->distribution_shape[0]['mode'],
                                    $node->distribution_shape[0]['max'],
                                    $simulationNumber
                                  );
                                  $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                  if ($index !== false) {
                                    $aux = $valoresPorNodo[$index]
                                  }else{
                                    $aux = false;
                                  }
                                  if (!$aux) {
                                    $valoresPorNodo[] = [
                                      "name"=> $node->name,
                                      "values"=> [$triangularNumber],
                                    ];
                                  } else {
                                    $values = $aux['values'];
                                    $values[] = $triangularNumber;
                                    $aux['values'] = $values;
                                  }
                                  $formula[] = '(' . $triangularNumber . ')';
                                  $csvData[$j] = array_merge($csvData[$j], array($node->name => $triangularNumber));
                                break;
                            case 'Binominal':
                                const binomialNumber = this.binomialOperation(
                                    $node->distribution_shape[0]['trials'],
                                    $node->distribution_shape[0]['probability'],
                                    $simulationNumber
                                  );
                                  $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                  if ($index !== false) {
                                    $aux = $valoresPorNodo[$index]
                                  }else{
                                    $aux = false;
                                  }
                                  if (!$aux) {
                                    $valoresPorNodo[] = [
                                      "name" => $node->name,
                                      "values" => [$binomialNumber],
                                    ];
                                  } else {
                                    $values = $aux['values'];
                                    $values[] = $binomialNumber;
                                    $aux['values'] = $values;
                                  }
                                  $formula[] = '(' . $binomialNumber . ')';
                                  $csvData[$j] = array_merge($csvData[$j], array($node->name => $binomialNumber));
                                break;
                            case 'Lognormal':
                                $lognormalNumber = $this->lognormalOperation(
                                    $node->distribution_shape[0]['mean'],
                                    $node->distribution_shape[0]['stDev'],
                                    $simulationNumber
                                  );
                                  $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                  if ($index !== false) {
                                    $aux = $valoresPorNodo[$index]
                                  }else{
                                    $aux = false;
                                  }
                                  if (!$aux) {
                                    $valoresPorNodo[] = [
                                      "name" => $node->name,
                                      "values" => [$lognormalNumber],
                                    ];
                                  } else {
                                    $values = $aux['values'];
                                    $values[] = $lognormalNumber;
                                    $aux['values'] = $values;
                                  }
                                  $formula[] = '(' . $lognormalNumber . ')';
                                  $csvData[$j] = array_merge($csvData[$j], array($node->name => $lognormalNumber));
                                break;
                            case 'Geometric':
                                $geometricNumber = $this->geometricalOperation(
                                    $node->distribution_shape[0]['probability'],
                                    $simulationNumber
                                  );
                                  $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                  if ($index !== false) {
                                    $aux = $valoresPorNodo[$index]
                                  }else{
                                    $aux = false;
                                  }
                                  if (!$aux) {
                                    $valoresPorNodo[] = [
                                      "name" => $node->name,
                                      "values" => [$geometricNumber],
                                    ];
                                  } else {
                                    $values = $aux['values'];
                                    $values[] = $geometricNumber;
                                    $aux['values'] = $values;
                                  }
                                  $formula[] = '(' . $geometricNumber . ')';
                                  $csvData[$j] = array_merge($csvData[$j], array($node->name => $geometricNumber));
                                break;
                            case 'Weibull':
                                $weibullNumber = $this->weibullOperation(
                                    $node->distribution_shape[0]['form'],
                                    $node->distribution_shape[0]['scale'],
                                    $simulationNumber
                                  );
                                  $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                  if ($index !== false) {
                                    $aux = $valoresPorNodo[$index]
                                  }else{
                                    $aux = false;
                                  }
                                  if (!$aux) {
                                    $valoresPorNodo[] = [
                                      "name" => $node->name,
                                      "values" => [$weibullNumber],
                                    ];
                                  } else {
                                    $values = $aux['values'];
                                    $values[] = $weibullNumber;
                                    $aux['values'] = $values;
                                  }
                                  $formula[] = '(' . $weibullNumber . ')';
                                  $csvData[$j] = array_merge($csvData[$j], array($node->name => $weibullNumber));
                                break;
                            case 'Beta':
                                $betaNumber = $this->betaOperation(
                                    $node->distribution_shape[0]['alpha'],
                                    $node->distribution_shape[0]['beta'],
                                    $simulationNumber
                                  );
                                  $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                  if ($index !== false) {
                                    $aux = $valoresPorNodo[$index]
                                  }else{
                                    $aux = false;
                                  }
                                  if (!$aux) {
                                    $valoresPorNodo[] = [
                                      "name" => $node->name,
                                      "values" => [$betaNumber],
                                    ];
                                  } else {
                                    $values = $aux['values'];
                                    $values[] = $betaNumber;
                                    $aux['values'] = $values;
                                  }
                                  $formula[] = '(' . $betaNumber . ')';
                                  $csvData[$j] = array_merge($csvData[$j], array($node->name => $betaNumber));
                                break;
                            case 'Hypergeometric':
                                $hypergeometricNumber = this.hypergeometricOperation(
                                    $node->distribution_shape[0]['population'],
                                    $node->distribution_shape[0]['success'],
                                    $node->distribution_shape[0]['trials'],
                                    $simulationNumber
                                  );
                                  $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                  if ($index !== false) {
                                    $aux = $valoresPorNodo[$index]
                                  }else{
                                    $aux = false;
                                  }
                                  if (!$aux) {
                                    $valoresPorNodo[] = [
                                      "name" => $node->name,
                                      "values" => [$hypergeometricNumber],
                                    ];
                                  } else {
                                    $values = $aux['values'];
                                    $values[] = $hypergeometricNumber;
                                    $aux['values'] = $values;
                                  }
                                  $formula[] = '(' . $hypergeometricNumber . ')';
                                  $csvData[$j] = array_merge($csvData[$j], array($node->name => $hypergeometricNumber));
                                break;
                            case 'Poisson':
                                $poissonNumber = $this->poissonOperation(
                                    $node->distribution_shape[0]['lamda'],
                                    $simulationNumber
                                  );
                                  $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                  if ($index !== false) {
                                    $aux = $valoresPorNodo[$index]
                                  }else{
                                    $aux = false;
                                  }
                                  if (!$aux) {
                                    $valoresPorNodo[] = [
                                      "name" => $node->name,
                                      "values" => [$poissonNumber],
                                    ];
                                  } else {
                                    $values = $aux['values'];
                                    $values[] = $poissonNumber;
                                    $aux['values'] = $values;
                                  }
                                  $formula[] = '(' . $poissonNumber . ')';
                                  $csvData[$j] = array_merge($csvData[$j], array($node->name => $poissonNumber));
                                break;
                            case 'Normal':
                                $randomNumberNormal = this.normalOperation(
                                    $node->distribution_shape[0]['mean'],
                                    $node->distribution_shape[0]['stDev'],
                                    $simulationNumber
                                  );
                                  $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                  if ($index !== false) {
                                    $aux = $valoresPorNodo[$index]
                                  }else{
                                    $aux = false;
                                  }
                                  if (!$aux) {
                                    $valoresPorNodo[] = [
                                      "name" => $node->name,
                                      "values" => [$randomNumberNormal],
                                    ];
                                  } else {
                                    $values = $aux['values'];
                                    $values[] = $randomNumberNormal;
                                    $aux['values'] = $values;
                                  }
                                  $formula[] = '(' . $randomNumberNormal . ')';
                                  $csvData[$j] = array_merge($csvData[$j], array($node->name => $randomNumberNormal));
                                break;
                            case 'Exponencial':
                                $randomNumberExponential = $this->exponentialOperation(
                                    $node->distribution_shape[0]['rate'],
                                    $simulationNumber
                                  );
                                  $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                  if ($index !== false) {
                                    $aux = $valoresPorNodo[$index]
                                  }else{
                                    $aux = false;
                                  }
                                  if (!$aux) {
                                    $valoresPorNodo[] = [
                                      "name" => $node->name,
                                      "values" => [$randomNumberExponential],
                                    ];
                                  } else {
                                    $values = $aux['values'];
                                    $values[] = $randomNumberExponential;
                                    $aux['values'] = $values;
                                  }
                                  $formula[] = '(' . $randomNumberExponential . ')';
                                  $csvData[$j] = array_merge($csvData[$j], array($node->name => $randomNumberExponential));
                                break;
                            default:
                                break;
                        }
                    }
                }else{
                    $formula2 = $this->recursiveCalculate($node);
                    $formula[] = '(' . $formula2 . ')';
                }

            }else{
                array_push($formula, $nodeId);
            }
        }
    }
    public function generateSimulation(Request $r)
    {
        $simulationNumber = $r->size;
        $project_id = $r->project_id;
        $nodesActive = $r->nodes_active;

        $project = Project::find($project_id);
        $tierCero = Node::where(['project_id'=>$project_id,'tier'=>0])->first();
        $valoresPorNodo = [];
        
        $formula = [];
        $csvData = [];
        $aux = null;

        for ($j=0; $j < $simulationNumber; $j++) { 
            // code...
            for ($i = 0; $i < count($tierCero->formula); $i++) {
                $nodeId = $tierCero->formula[$i];
                if (gettype($nodeId) == 'integer') {

                    $node = Node::find($nodeId);
                    $csvData[$j] = array_merge($csvData[$j], array("id" => $this->simulationId));
                    if ($node->type == 1) {
                        if (!$node->isActive || $node->isActive == false) {
                          $value =
                            $node->unite == null || $node->unite == ""
                              ? '0'
                              : $node->unite;
                          $formula[] = $value;

                          $index = array_search($node->name, array_column($valoresPorNodo, "name"));

                          if ($index !== false) {
                            $aux = $valoresPorNodo[$index]
                          }else{
                            $aux = false;
                          }
                          // $csvData[$j] = array_merge($csvData[$j], array([$node->name] => $value));
                          $csvData[$j] = array_merge($csvData[$j], array($node->name => $value));
                          if (!$aux) {
                            $valoresPorNodo[] = ["name" => $node->name, "values" => [$value]];
                          } else {
                            $values = $aux['values'];
                            $values[] = $value;
                            $aux['values'] = $values;
                          }
                        } else {
                            switch ($node->distribution_shape[0]['name']) {
                                case 'Uniforme':
                                    $randomNumber = $this->uniformOperation(
                                        $node->distribution_shape[0]['min'],
                                        $node->distribution_shape[0]['max'],
                                        $simulationNumber
                                      );
                                      $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                      if ($index !== false) {
                                        $aux = $valoresPorNodo[$index]
                                      }else{
                                        $aux = false;
                                      }
                                      if (!$aux) {
                                        $valoresPorNodo[] = [
                                          "name"=> $node->name,
                                          "values" => [$randomNumber],
                                        ];
                                      } else {
                                        $values = $aux['values'];
                                        $values[] = $randomNumber;
                                        $aux['values'] = $values;
                                      }
                                      $formula[] = '(' . $randomNumber . ')';
                                      $csvData[$j] = array_merge($csvData[$j], array($node->name => $randomNumber));
                                    break;
                                case 'Triangular':
                                    $triangularNumber = $this->triangularOperation(
                                        $node->distribution_shape[0]['min'],
                                        $node->distribution_shape[0]['mode'],
                                        $node->distribution_shape[0]['max'],
                                        $simulationNumber
                                      );
                                      $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                      if ($index !== false) {
                                        $aux = $valoresPorNodo[$index]
                                      }else{
                                        $aux = false;
                                      }
                                      if (!$aux) {
                                        $valoresPorNodo[] = [
                                          "name"=> $node->name,
                                          "values"=> [$triangularNumber],
                                        ];
                                      } else {
                                        $values = $aux['values'];
                                        $values[] = $triangularNumber;
                                        $aux['values'] = $values;
                                      }
                                      $formula[] = '(' . $triangularNumber . ')';
                                      $csvData[$j] = array_merge($csvData[$j], array($node->name => $triangularNumber));
                                    break;
                                case 'Binominal':
                                    const binomialNumber = this.binomialOperation(
                                        $node->distribution_shape[0]['trials'],
                                        $node->distribution_shape[0]['probability'],
                                        $simulationNumber
                                      );
                                      $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                      if ($index !== false) {
                                        $aux = $valoresPorNodo[$index]
                                      }else{
                                        $aux = false;
                                      }
                                      if (!$aux) {
                                        $valoresPorNodo[] = [
                                          "name" => $node->name,
                                          "values" => [$binomialNumber],
                                        ];
                                      } else {
                                        $values = $aux['values'];
                                        $values[] = $binomialNumber;
                                        $aux['values'] = $values;
                                      }
                                      $formula[] = '(' . $binomialNumber . ')';
                                      $csvData[$j] = array_merge($csvData[$j], array($node->name => $binomialNumber));
                                    break;
                                case 'Lognormal':
                                    $lognormalNumber = $this->lognormalOperation(
                                        $node->distribution_shape[0]['mean'],
                                        $node->distribution_shape[0]['stDev'],
                                        $simulationNumber
                                      );
                                      $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                      if ($index !== false) {
                                        $aux = $valoresPorNodo[$index]
                                      }else{
                                        $aux = false;
                                      }
                                      if (!$aux) {
                                        $valoresPorNodo[] = [
                                          "name" => $node->name,
                                          "values" => [$lognormalNumber],
                                        ];
                                      } else {
                                        $values = $aux['values'];
                                        $values[] = $lognormalNumber;
                                        $aux['values'] = $values;
                                      }
                                      $formula[] = '(' . $lognormalNumber . ')';
                                      $csvData[$j] = array_merge($csvData[$j], array($node->name => $lognormalNumber));
                                    break;
                                case 'Geometric':
                                    $geometricNumber = $this->geometricalOperation(
                                        $node->distribution_shape[0]['probability'],
                                        $simulationNumber
                                      );
                                      $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                      if ($index !== false) {
                                        $aux = $valoresPorNodo[$index]
                                      }else{
                                        $aux = false;
                                      }
                                      if (!$aux) {
                                        $valoresPorNodo[] = [
                                          "name" => $node->name,
                                          "values" => [$geometricNumber],
                                        ];
                                      } else {
                                        $values = $aux['values'];
                                        $values[] = $geometricNumber;
                                        $aux['values'] = $values;
                                      }
                                      $formula[] = '(' . $geometricNumber . ')';
                                      $csvData[$j] = array_merge($csvData[$j], array($node->name => $geometricNumber));
                                    break;
                                case 'Weibull':
                                    $weibullNumber = $this->weibullOperation(
                                        $node->distribution_shape[0]['form'],
                                        $node->distribution_shape[0]['scale'],
                                        $simulationNumber
                                      );
                                      $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                      if ($index !== false) {
                                        $aux = $valoresPorNodo[$index]
                                      }else{
                                        $aux = false;
                                      }
                                      if (!$aux) {
                                        $valoresPorNodo[] = [
                                          "name" => $node->name,
                                          "values" => [$weibullNumber],
                                        ];
                                      } else {
                                        $values = $aux['values'];
                                        $values[] = $weibullNumber;
                                        $aux['values'] = $values;
                                      }
                                      $formula[] = '(' . $weibullNumber . ')';
                                      $csvData[$j] = array_merge($csvData[$j], array($node->name => $weibullNumber));
                                    break;
                                case 'Beta':
                                    $betaNumber = $this->betaOperation(
                                        $node->distribution_shape[0]['alpha'],
                                        $node->distribution_shape[0]['beta'],
                                        $simulationNumber
                                      );
                                      $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                      if ($index !== false) {
                                        $aux = $valoresPorNodo[$index]
                                      }else{
                                        $aux = false;
                                      }
                                      if (!$aux) {
                                        $valoresPorNodo[] = [
                                          "name" => $node->name,
                                          "values" => [$betaNumber],
                                        ];
                                      } else {
                                        $values = $aux['values'];
                                        $values[] = $betaNumber;
                                        $aux['values'] = $values;
                                      }
                                      $formula[] = '(' . $betaNumber . ')';
                                      $csvData[$j] = array_merge($csvData[$j], array($node->name => $betaNumber));
                                    break;
                                case 'Hypergeometric':
                                    $hypergeometricNumber = this.hypergeometricOperation(
                                        $node->distribution_shape[0]['population'],
                                        $node->distribution_shape[0]['success'],
                                        $node->distribution_shape[0]['trials'],
                                        $simulationNumber
                                      );
                                      $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                      if ($index !== false) {
                                        $aux = $valoresPorNodo[$index]
                                      }else{
                                        $aux = false;
                                      }
                                      if (!$aux) {
                                        $valoresPorNodo[] = [
                                          "name" => $node->name,
                                          "values" => [$hypergeometricNumber],
                                        ];
                                      } else {
                                        $values = $aux['values'];
                                        $values[] = $hypergeometricNumber;
                                        $aux['values'] = $values;
                                      }
                                      $formula[] = '(' . $hypergeometricNumber . ')';
                                      $csvData[$j] = array_merge($csvData[$j], array($node->name => $hypergeometricNumber));
                                    break;
                                case 'Poisson':
                                    $poissonNumber = $this->poissonOperation(
                                        $node->distribution_shape[0]['lamda'],
                                        $simulationNumber
                                      );
                                      $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                      if ($index !== false) {
                                        $aux = $valoresPorNodo[$index]
                                      }else{
                                        $aux = false;
                                      }
                                      if (!$aux) {
                                        $valoresPorNodo[] = [
                                          "name" => $node->name,
                                          "values" => [$poissonNumber],
                                        ];
                                      } else {
                                        $values = $aux['values'];
                                        $values[] = $poissonNumber;
                                        $aux['values'] = $values;
                                      }
                                      $formula[] = '(' . $poissonNumber . ')';
                                      $csvData[$j] = array_merge($csvData[$j], array($node->name => $poissonNumber));
                                    break;
                                case 'Normal':
                                    $randomNumberNormal = this.normalOperation(
                                        $node->distribution_shape[0]['mean'],
                                        $node->distribution_shape[0]['stDev'],
                                        $simulationNumber
                                      );
                                      $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                      if ($index !== false) {
                                        $aux = $valoresPorNodo[$index]
                                      }else{
                                        $aux = false;
                                      }
                                      if (!$aux) {
                                        $valoresPorNodo[] = [
                                          "name" => $node->name,
                                          "values" => [$randomNumberNormal],
                                        ];
                                      } else {
                                        $values = $aux['values'];
                                        $values[] = $randomNumberNormal;
                                        $aux['values'] = $values;
                                      }
                                      $formula[] = '(' . $randomNumberNormal . ')';
                                      $csvData[$j] = array_merge($csvData[$j], array($node->name => $randomNumberNormal));
                                    break;
                                case 'Exponencial':
                                    $randomNumberExponential = $this->exponentialOperation(
                                        $node->distribution_shape[0]['rate'],
                                        $simulationNumber
                                      );
                                      $index = array_search($node->name, array_column($valoresPorNodo, "name"));
                                      if ($index !== false) {
                                        $aux = $valoresPorNodo[$index]
                                      }else{
                                        $aux = false;
                                      }
                                      if (!$aux) {
                                        $valoresPorNodo[] = [
                                          "name" => $node->name,
                                          "values" => [$randomNumberExponential],
                                        ];
                                      } else {
                                        $values = $aux['values'];
                                        $values[] = $randomNumberExponential;
                                        $aux['values'] = $values;
                                      }
                                      $formula[] = '(' . $randomNumberExponential . ')';
                                      $csvData[$j] = array_merge($csvData[$j], array($node->name => $randomNumberExponential));
                                    break;
                                default:
                                    break;
                            }
                        }
                    }else{
                        $formula2 = $this->recursiveCalculate($node);
                        $formula[] = '(' . $formula2 . ')';
                        /*$csvData[$j] = array_merge($csvData[$j], array($node->name => eval('return ' . implode('', array_map('strval', $formula2)) . ';')));

                        if (Object.keys(data).length !== 0) {
                          csvData[j] = {
                            ...csvData[j],
                            ...Object.keys(data).reduce((acc: any, key) => {
                              if (!(key in csvData[j])) {
                                acc[key] = data[key];
                              }
                              return acc;
                            }, {}),
                          };
                        }*/
                    }

                }else{
                    array_push($formula, $nodeId);
                }
            }

        }
    }
    //
    private function uniformOperation($minValue, $maxValue, $simulationNumber) {
        $min = $minValue;
        $max = $maxValue;

        // Generar muestras de la distribución
        $s = [];
        for ($i = 0; $i < $simulationNumber; $i++) {
            array_push($s, 
                $min + (mt_rand() / mt_getrandmax()) * ($max - $min)
            );
        }

        // Verificar que todos los valores están dentro del intervalo dado
        // Puedes descomentar la siguiente línea para verificar
        // echo array_reduce($s, function($carry, $item) use ($min, $max) { return $carry && ($item >= $min && $item < $max); }, true);

        $binWidth = ($max - $min) / 15;

        // Crear un array con valores distribuidos uniformemente
        $arrayOperation = [];
        for ($i = 0; $i < 50; $i++) {
            array_push($arrayOperation, 
                $min + $i * $binWidth
            );
        }

        // Retornar un valor aleatorio del array
        return !empty($arrayOperation) ? 
               $arrayOperation[floor(mt_rand(0, count($arrayOperation) - 1))] : 
               null; // Return null if the array is empty
    }

    private function normalOperation($meanOperation, $stDevOperation, $simulationNumber) {
        // Definir la media y la desviación estándar
        $mu = $meanOperation;
        $sigma = $stDevOperation;
        $samples = $simulationNumber;

        // Generar una distribución normal
        $s = [];
        for ($i = 0; $i < $samples; $i++) {
            array_push($s, 
                $mu + 
                $sigma * sqrt(-2.0 * log(mt_rand() / mt_getrandmax())) * 
                cos(2.0 * M_PI * mt_rand() / mt_getrandmax())
            );
        }

        // Crear el histograma
        $histogram = array_fill(0, $samples, 0);
        foreach ($s as $value) {
            $index = floor(((($value - $mu + 5 * $sigma) / (10 * $sigma)) * 100));
            if ($index >= 0 && $index < count($histogram)) {
                $histogram[$index]++;
            }
        }

        // Normalizar el histograma
        $binWidth = (10 * $sigma) / 100;
        foreach ($histogram as &$value) {
            $value /= ($binWidth * count($s));
        }
        unset($value); // Break the reference with the last element

        // Crear la curva de la función de densidad de probabilidad
        $x = [];
        for ($i = 0; $i < 100; $i++) {
            array_push($x, 
                $mu - 5 * $sigma + ($i * (10 * $sigma)) / 100
            );
        }

        // Filtrar los valores donde el histograma es mayor que cero
        $x = array_filter($x, function ($_, $i) use ($histogram) {
            return isset($histogram[$i]) && $histogram[$i] > 0;
        }, ARRAY_FILTER_USE_BOTH);

        // Convertir los resultados del filtro en un array indexado
        $x = array_values($x);

        // Retornar un valor aleatorio de la curva de la función de densidad de probabilidad
        return !empty($x) ? 
               $x[floor(mt_rand(0, count($x) - 1))] : 
               null; // Return null if the array is empty
    }

    private function exponentialOperation($rateOperation, $simulationNumber) {
        // Escala de la distribución exponencial
        $rate = $rateOperation; // Cambia este valor para ajustar la escala

        // Dibujar muestras de la distribución exponencial
        $s = [];
        for ($i = 0; $i < $simulationNumber; $i++) {
            array_push($s, 
                -$rate * log(1.0 - (mt_rand() / mt_getrandmax()))
            );
        }

        // Crear el histograma
        $histogram = array_fill(0, 50, 0);
        foreach ($s as $value) {
            $index = min(floor($value / (10 / 50)), count($histogram) - 1);
            $histogram[$index]++;
        }

        // Normalizar el histograma
        $binWidth = 10 / 50;
        foreach ($histogram as &$value) {
            $value /= ($binWidth * count($s));
        }
        unset($value); // Break the reference with the last element

        // Crear bins para el histograma
        $bins = [];
        for ($i = 0; $i < count($histogram); $i++) {
            array_push($bins, 
                $i * $binWidth
            );
        }

        // Retornar un valor aleatorio de los bins del histograma
        return !empty($bins) ? 
               $bins[floor(mt_rand(0, count($bins) - 1))] : 
               null; // Return null if the array is empty
    }

    private function triangularOperation($min, $mode, $max, $simulationNumber) {
        // Función para generar números aleatorios con distribución triangular
        function triangularDistribution($simulationNumber, $low, $mode, $high) {
            $triangularSamples = [];
            for ($i = 0; $i < $simulationNumber; $i++) {
                $u = mt_rand() / mt_getrandmax();
                $f = ($mode - $low) / ($high - $low);
                if ($u <= $f) {
                    array_push($triangularSamples, 
                        $low + sqrt($u * ($high - $low) * ($mode - $low))
                    );
                } else {
                    array_push($triangularSamples, 
                        $high - sqrt((1 - $u) * ($high - $low) * ($high - $mode))
                    );
                }
            }
            return $triangularSamples;
        }

        // Definir parámetros de la distribución triangular
        // Generar números aleatorios con distribución triangular
        $triangularSamples = triangularDistribution($simulationNumber, $min, $mode, $max);

        // Retornar un valor aleatorio de los números generados
        return !empty($triangularSamples) ? 
               $triangularSamples[floor(mt_rand(0, count($triangularSamples) - 1))] : 
               null; // Return null if the array is empty
    }

    private function poissonOperation($lambda, $simulationNumber) {
        // Función para generar números aleatorios con distribución de Poisson
        function poissonDistribution($simulationNumber, $lambda) {
            $poissonSamples = [];
            for ($i = 0; $i < $simulationNumber; $i++) {
                $L = exp(-$lambda);
                $k = 0;
                $p = 1.0;
                do {
                    $k++;
                    $p *= mt_rand() / mt_getrandmax();
                } while ($p > $L);
                array_push($poissonSamples, $k - 1);
            }
            return $poissonSamples;
        }

        // Definir parámetros de la distribución de Poisson
        // Generar números aleatorios con distribución de Poisson
        $poissonSamples = poissonDistribution($simulationNumber, $lambda);

        // Retornar un valor aleatorio de los números generados
        return !empty($poissonSamples) ? 
               $poissonSamples[floor(mt_rand(0, count($poissonSamples) - 1))] : 
               null; // Return null if the array is empty
    }

    private function binomialOperation($trials, $probability, $simulationNumber) {
        // Función para generar números aleatorios con distribución binomial
        function binomialDistribution($simulationNumber, $n, $p) {
            $binomialSamples = [];
            for ($i = 0; $i < $simulationNumber; $i++) {
                $successes = 0;
                for ($j = 0; $j < $n; $j++) {
                    if ((mt_rand() / mt_getrandmax()) < $p) {
                        $successes++;
                    }
                }
                array_push($binomialSamples, $successes);
            }
            return $binomialSamples;
        }

        // Definir parámetros de la distribución binomial
        // Generar números aleatorios con distribución binomial
        $binomialSamples = binomialDistribution($simulationNumber, $trials, $probability);

        // Retornar un valor aleatorio de los números generados
        return !empty($binomialSamples) ? 
               $binomialSamples[floor(mt_rand(0, count($binomialSamples) - 1))] : 
               null; // Return null if the array is empty
    }

    private function lognormalOperation($mean, $stDev) {
        // Parámetros de la distribución logarítmico normal
        $mu = log($mean); // Media logarítmica
        $sigma = $stDev / $mean; // Desviación estándar logarítmica

        // Función de densidad de probabilidad (PDF) de la distribución logarítmica normal
        function lognormalPDF($x, $mu, $sigma) {
            $coefficient = 1 / ($x * $sigma * sqrt(2 * M_PI));
            $exponent = -pow((log($x) - $mu), 2) / (2 * pow($sigma, 2));
            return $coefficient * exp($exponent);
        }

        // Datos para el gráfico
        $data = [];

        // Calcular datos para el gráfico
        $step = 2; // Mostrar cada 2 puntos en el eje x
        for ($x = 1; $x <= 200; $x += 0.1 * $step) {
            $pdf = lognormalPDF($x, $mu, $sigma);
            array_push($data, $pdf);
        }

        // Retornar un valor aleatorio de los datos generados
        return !empty($data) ? 
               $data[floor(mt_rand(0, count($data) - 1))] : 
               null; // Return null if the array is empty
    }

    private function geometricalOperation($probability, $simulationNumber) {
        // Parámetros de la distribución geométrica
        $p = $probability; // Probabilidad de éxito en cada intento

        // Generar muestras de la distribución geométrica
        $samples = [];
        for ($i = 0; $i < $simulationNumber; $i++) {
            $attempts = 1;
            while (mt_rand() / mt_getrandmax() >= $p) {
                $attempts++;
            }
            array_push($samples, $attempts);
        }

        // Retornar un valor aleatorio de las muestras generadas
        return !empty($samples) ? 
               $samples[floor(mt_rand(0, count($samples) - 1))] : 
               null; // Return null if the array is empty
    }

    private function weibullOperation($k, $lambda, $simulationNumber) {
        function generateWeibullSamples($k, $lambda, $simulationNumber) {
            $samples = [];
            for ($i = 0; $i < $simulationNumber; $i++) {
                $u = mt_rand() / mt_getrandmax();
                $sample = $lambda * pow(-log(1 - $u), 1 / $k);
                array_push($samples, $sample);
            }
            return $samples;
        }

        // Generar muestras de la distribución de Weibull
        $samples = generateWeibullSamples($k, $lambda, $simulationNumber);

        // Retornar un valor aleatorio de las muestras generadas
        return !empty($samples) ? 
               $samples[floor(mt_rand(0, count($samples) - 1))] : 
               null; // Return null if the array is empty
    }

    private function betaOperation($alpha, $beta, $simulationNumber) {
        // Parámetros de la distribución beta
        $alpha = $alpha; // Parámetro de forma
        $beta = $beta; // Parámetro de forma

        // Generar muestras de la distribución beta
        $samples = [];
        for ($i = 0; $i < $simulationNumber; $i++) {
            $samples[] = pow(mt_rand() / mt_getrandmax(), $alpha) * 
                         pow(1 - mt_rand() / mt_getrandmax(), $beta);
        }

        // Retornar un valor aleatorio de las muestras generadas
        return !empty($samples) ? 
               $samples[floor(mt_rand(0, count($samples) - 1))] : 
               null; // Return null if the array is empty
    }

    private function hypergeometricOperation($M, $n, $N, $simulationNumber) {
        function generateHypergeometricSamples($M, $n, $N, $simulationNumber) {
            $samples = [];
            for ($i = 0; $i < $simulationNumber; $i++) {
                $successes = 0;
                $population = array_fill(0, $M, 0);
                foreach (range(0, $n - 1) as $j) {
                    $population[$j] = 1;
                }
                shuffle($population);
                for ($j = 0; $j < $N; $j++) {
                    if ($population[$j] == 1) {
                        $successes++;
                    }
                }
                array_push($samples, $successes);
            }
            return $samples;
        }

        // Generar muestras de la distribución hipergeométrica
        $samples = generateHypergeometricSamples($M, $n, $N, $simulationNumber);

        // Retornar un valor aleatorio de las muestras generadas
        return !empty($samples) ? 
               $samples[floor(mt_rand(0, count($samples) - 1))] : 
               null; // Return null if the array is empty
    }


}
