<?php

namespace Druid628\RulesDemo\RulesDemoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use BlueShamrock\Symfony\BsdRADBundle\Controller\Controller as BSDController;
use Ruler\RuleBuilder;
use Ruler\Context;

class DefaultController extends BSDController
{
    /**
     * @Route("/demo/execute/{price}/{use}")
     * @Template()
     */
    public function execAction($price, $use)
    {
        $controlData = [
            'worth'   => 4,
            'utility' => 10
        ];

        $context = new Context( array_merge( $controlData, [ 'use' => $use, 'price' => $price ]));
        $output  = "Doesn't fit your criteria I would not recommend buying it";

        $rb   = $this->get('rule.builder');
        $rule = $rb->create(
            $rb->logicalAnd( 
                $rb['price']->LessThanOrEqualTo($rb['worth']),
                $rb['use']->GreaterThanOrEqualTo($rb['utility'])
            ), function() use (&$output){
                $output = "This is what you're looking for. Buy it";
            }
        );

        $rule->execute($context);

        return new Response($output);
    }

    /**
     * @Route("/demo/evaluate/{price}/{use}")
     * @Template()
     */
    public function evalAction($price, $use)
    {
        $controlData = [
            'worth'   => 4,
            'utility' => 9
        ];

        $context = new Context( array_merge( $controlData, [ 'use' => $use, 'price' => $price ]));

        $rb   = $this->get('rule.builder');
        $rule = $rb->create(
            $rb->logicalAnd(
                $rb['price']->LessThanOrEqualTo($rb['worth']),
                $rb['use']->GreaterThanOrEqualTo($rb['utility'])
            )
        );

        $output = $rule->evaluate($context);
        $output = ($output) ? "Valid" : "Invalid";

        return new Response( $output);

    }

}
