<?php

namespace Peridot\Core;

use Closure;
use Evenement\EventEmitterInterface;
use Evenement\EventEmitterTrait;

/**
 * Class AbstractSpec
 * @package Peridot\Core
 */
abstract class AbstractSpec implements SpecInterface
{
    /**
     * The spec definition as a callable.
     *
     * @var callable
     */
    protected $definition;

    /**
     * A collection of functions to run before specs execute.
     *
     * @var array
     */
    protected $setUpFns = [];

    /**
     * A collection of functions to run after specs execute.
     *
     * @var array
     */
    protected $tearDownFns = [];

    /**
     * @var string
     */
    protected $description;

    /**
     * @var SpecInterface
     */
    protected $parent;

    /**
     * @var bool|null
     */
    protected $pending = null;

    /**
     * @var \Evenement\EventEmitterInterface
     */
    protected $eventEmitter;

    /**
     * This is oddly named so using it will only be VERY intentional
     *
     * @var Scope
     */
    protected $peridotScopeVariableDoNotTouchThanks;

    /**
     * Constructor.
     *
     * @param string $description
     * @param callable $definition
     */
    public function __construct($description, callable $definition)
    {
        $this->definition = $definition;
        $this->description = $description;
        $this->peridotScopeVariableDoNotTouchThanks = new Scope();
    }

    /**
     * {@inheritdoc}
     *
     * @param callable $setupFn
     */
    public function addSetUpFunction(callable $setupFn)
    {
        $this->setUpFns[] = Closure::bind(
            $setupFn,
            $this->peridotScopeVariableDoNotTouchThanks,
            $this->peridotScopeVariableDoNotTouchThanks
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param callable $tearDownFn
     */
    public function addTearDownFunction(callable $tearDownFn)
    {
        $this->tearDownFns[] = Closure::bind(
            $tearDownFn,
            $this->peridotScopeVariableDoNotTouchThanks,
            $this->peridotScopeVariableDoNotTouchThanks
        );
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     *
     * @return callable
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * {@inheritdoc}
     *
     * @param SpecInterface $parent
     * @return mixed|void
     */
    public function setParent(SpecInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * {@inheritdoc}
     *
     * @return SpecInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getTitle()
    {
        $parts = [];
        $node = $this;
        while ($node != null) {
            array_unshift($parts, $node->getDescription());
            $node = $node->getParent();
        }
        return implode(' ' ,$parts);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool|null
     */
    public function getPending()
    {
        return $this->pending;
    }

    /**
     * {@inheritdoc}
     *
     * @param bool $state
     */
    public function setPending($state)
    {
        $this->pending = (bool)$state;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getSetUpFunctions()
    {
        return $this->setUpFns;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getTearDownFunctions()
    {
        return $this->tearDownFns;
    }

    /**
     * {@inheritdoc}
     *
     * @return \Peridot\Core\Scope
     */
    public function getScope()
    {
        return $this->peridotScopeVariableDoNotTouchThanks;
    }

    /**
     * @param \Evenement\EventEmitterInterface $eventEmitter
     */
    public function setEventEmitter(EventEmitterInterface $eventEmitter)
    {
        $this->eventEmitter = $eventEmitter;
        return $this;
    }

    /**
     * @return \Evenement\EventEmitterInterface
     */
    public function getEventEmitter()
    {
        return $this->eventEmitter;
    }
}
