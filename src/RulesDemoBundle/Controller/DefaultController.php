<?php

namespace Druid628\RulesDemo\RulesDemoBundle\Controller;

use Druid628\RulesDemo\RulesDemoBundle\CourtConstants;
use Druid628\RulesDemo\RulesDemoBundle\Entity\Rule;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\SubmitButton;
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
     * @Route("/demo/execute", name="demo_execute")
     * @Template("RulesDemoBundle:Default:demo.html.twig")
     */
    public function demoAction(Request $request)
    {
        $form = $this->createFormBuilder(null)
            ->add('use', ChoiceType::class, [
                'label'   => 'Utility',
                'choices' => ['1' => 'Not very useful', '5' => 'Useful', '10' => 'Supremely Useful'],
            ])
            ->add('price', ChoiceType::class, [
                'label'   => 'Price',
                'choices' => [
                    '1'  => 'Dirt Cheap',
                    '5'  => 'Eh',
                    '10' => 'Better Sell the House',
                ],
            ])
            ->add('submit', ButtonType::class, ['attr' => ['class' => 'submit']])
            ->setAction($this->generateUrl('demo_execute'))
            ->getForm();

        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);
            $data = $form->getData();

            $controlData = [
                'worth'   => 4,
                'utility' => 10,
            ];

            $context = new Context(array_merge($controlData, ['use' => $data['use'], 'price' => $data['price']]));
            $output  = "Doesn't fit your criteria I would not recommend buying it";

            $rb   = $this->get('rule.builder');
            $rule = $rb->create(
                $rb->logicalAnd(
                    $rb['price']->LessThanOrEqualTo($rb['worth']),
                    $rb['use']->GreaterThanOrEqualTo($rb['utility'])
                ), function () use (&$output) {
                $output = "This is what you're looking for. Buy it";
            }
            );

            $output = $rule->execute($context);

            return $this->render("RulesDemoBundle:Default:evaluation.html.twig", ['output' => $output]);

        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/demo/court", name="court_demo")
     * @Template("RulesDemoBundle:Default:court.html.twig")
     */
    public function courtAction(Request $request)
    {
        $districts = array_merge(CourtConstants::LOCAL_DISTRICTS, CourtConstants::REMOTE_DISTRICTS);
        sort($districts);
        $districts = array_combine(array_values($districts), array_values($districts));

        $form = $this->createFormBuilder()
            ->add('district', ChoiceType::class, [
                'label'   => 'District',
                'choices' => $districts,
            ])
            ->add('amount', TextType::class, ['label' => 'Bond Amount'])
            ->add('submit', SubmitType::class, ['attr' => ['class' => 'submit']])
            ->setAction($this->generateUrl('court_demo'))
            ->setMethod('POST')
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            $court = $form->getData();
            $court['remote_districts'] = CourtConstants::REMOTE_DISTRICTS ;
            $courtContext = new Context($court);
            $rule = $this->getCourtRule();

            if ( $rule->evaluate($courtContext) )  {
                $court['amount'] += 25;
            }

            return [ 'court' => $court ];

        }

        return ['form' => $form->createView()];

    }

    /**
     * @Route("/demo/execute/{price}/{use}")
     * @Template()
     */
    public function execAction($price, $use)
    {
        $controlData = [
            'worth'   => 4,
            'utility' => 10,
        ];

        $context = new Context(array_merge($controlData, ['use' => $use, 'price' => $price]));
        $output  = "Doesn't fit your criteria I would not recommend buying it";

        $rb   = $this->get('rule.builder');
        $rule = $rb->create(
            $rb->logicalAnd(
                $rb['price']->LessThanOrEqualTo($rb['worth']),
                $rb['use']->GreaterThanOrEqualTo($rb['utility'])
            ), function () use (&$output) {
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
            'utility' => 9,
        ];

        $context = new Context(array_merge($controlData, ['use' => $use, 'price' => $price]));

        $rb   = $this->get('rule.builder');
        $rule = $rb->create(
            $rb->logicalAnd(
                $rb['price']->LessThanOrEqualTo($rb['worth']),
                $rb['use']->GreaterThanOrEqualTo($rb['utility'])
            )
        );

        $output = $rule->evaluate($context);
        $output = ($output) ? "Valid" : "Invalid";

        return new Response($output);

    }

    protected function getCourtRule()
    {
        $rb   = $this->get('rule.builder');
        $rule = $rb->create(
                 $rb['remote_districts']->ContainsSubset($rb['district'])
//             $rb->LogicalOr(
//                 $rb['district']->EqualTo(CourtConstants::REMOTE_DISTRICTS[0]),
//                 $rb['district']->EqualTo(CourtConstants::REMOTE_DISTRICTS[1]),
//                 $rb['district']->EqualTo(CourtConstants::REMOTE_DISTRICTS[2])
//             )
//            , function () use (&$court) {
//            $court['amount'] += 25;
//
//            }
        );

        return $rule;
    }
}
