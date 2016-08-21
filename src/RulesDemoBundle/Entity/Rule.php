<?php

namespace Druid628\RulesDemo\RulesDemoBundle\Entity;

use BlueShamrock\Symfony\BsdRADBundle\Entity\SluggableTrait;
use Doctrine\ORM\Mapping as ORM;
use BlueShamrock\Symfony\BsdRADBundle\Annotation\Sluggable;

/**
 * Rule
 *
 * @ORM\Table(name="rule")
 * @ORM\Entity(repositoryClass="Druid628\RulesDemo\RulesDemoBundle\Repository\RuleRepository")
 * @Sluggable("name")
 */
class Rule
{

    use SluggableTrait;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="client", type="string", length=100)
     */
    private $client;

    /**
     * @var string
     *
     * @ORM\Column(name="rule", type="text")
     */
    private $rule;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Rule
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set client
     *
     * @param string $client
     *
     * @return Rule
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return string
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set rule
     *
     * @param string $rule
     *
     * @return Rule
     */
    public function setRule($rule)
    {
        $this->rule = $rule;

        return $this;
    }

    /**
     * Get rule
     *
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }
}

