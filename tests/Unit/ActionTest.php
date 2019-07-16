<?php

namespace HongyukejiTests\Unit;

use HongyukejiTests\DummyClass;
use PHPUnit\Framework\TestCase;
use TorMorten\Hongyukeji\Hooks;

class ActionTest extends TestCase
{
    public function setUp(): void
    {
        $this->hooks = new Hooks();
    }

    /**
     * @test
     */
    public function it_can_hook_a_callable()
    {
        $this->hooks->addAction('my_awesome_action', function () {
            echo 'Action Fired, Baby!';
        });
        $this->expectOutputString('Action Fired, Baby!');
        $this->hooks->action('my_awesome_action');
    }

    /**
     * @test
     */
    public function it_can_hook_an_array()
    {
        $class = new class('DummyClass')
        {
            public function write()
            {
                echo 'Action Fired, Baby!';
            }
        };
        $this->hooks->addAction('my_amazing_action', [$class, 'write']);
        $this->expectOutputString('Action Fired, Baby!');
        $this->hooks->action('my_amazing_action');
    }

    /**
     * @test
     */
    public function a_hook_fires_even_if_there_are_two_listeners_with_the_same_priority()
    {
        $this->hooks->addAction('my_great_action', function () {
            echo 'Action Fired, Baby!';
        }, 20);

        $this->hooks->addAction('my_great_action', function () {
            echo 'Action Fired Again, Baby!';
        }, 20);

        $this->expectOutputString('Action Fired, Baby!Action Fired Again, Baby!');

        $this->hooks->action('my_great_action');
    }

    /**
     * @test
     */
    public function listeners_are_sorted_by_priority()
    {
        $this->hooks->addAction('my_great_action', function () {
            echo 'Action Fired, Baby!';
        }, 20);

        $this->hooks->addAction('my_great_action', function () {
            echo 'Action Fired, Baby!';
        }, 12);

        $this->hooks->addAction('my_great_action', function () {
            echo 'Action Fired, Baby!';
        }, 8);

        $this->hooks->addAction('my_great_action', function () {
            echo 'Action Fired, Baby!';
        }, 40);

        $this->assertEquals($this->hooks->getAction()->getListeners()->values()[0]['priority'], 8);
        $this->assertEquals($this->hooks->getAction()->getListeners()->values()[1]['priority'], 12);
        $this->assertEquals($this->hooks->getAction()->getListeners()->values()[2]['priority'], 20);
        $this->assertEquals($this->hooks->getAction()->getListeners()->values()[3]['priority'], 40);
    }

    /**
     * @test
     */
    public function a_single_action_is_removed()
    {
        // check the collection has 1 item
        $this->hooks->addAction('my_great_action', 'my_great_function', 30, 1);
        $this->hooks->addAction('my_great_action', 'my_great_function', 10, 1);
        $this->assertEquals($this->hooks->getAction()->getListeners()->where('hook', 'my_great_action')->count(), 2);

        // check removeAction removes the correct action
        $this->hooks->removeAction('my_great_action', 'my_great_function', 30);
        $this->assertEquals($this->hooks->getAction()->getListeners()->where('hook', 'my_great_action')->count(), 1);
        // check that the action with priority 10 still exists in the collection (only the action with priority 30 should've been removed)
        $this->assertEquals($this->hooks->getAction()->getListeners()->where('hook', 'my_great_action')->values()[0]['priority'], 10);
    }

    /**
     * @test
     */
    public function all_actions_removed()
    {
        // check the collection has 3 items before checking they're removed
        $this->hooks->addAction('my_great_action', 'my_great_function', 30, 1);
        $this->hooks->addAction('my_great_action', 'my_other_great_function', 30, 1);
        $this->hooks->addAction('my_great_action_2', 'my_great_function', 30, 1);
        $this->assertEquals($this->hooks->getAction()->getListeners()->count(), 3);

        // check removeFilter removes the filter
        $this->hooks->removeAllActions();
        $this->assertEquals($this->hooks->getAction()->getListeners()->count(), 0);
    }

    /**
     * @test
     */
    public function all_actions_removed_by_hook()
    {
        // check the collection has 3 items before checking they're removed correctly
        $this->hooks->addAction('my_great_action', 'my_great_function', 30, 1);
        $this->hooks->addAction('my_great_action', 'my_other_great_function', 30, 1);
        $this->hooks->addAction('my_great_action_2', 'my_great_function', 30, 1);
        $this->assertEquals($this->hooks->getAction()->getListeners()->count(), 3);

        // check removeAction removes the filter
        $this->hooks->removeAllActions('my_great_action');
        $this->assertEquals($this->hooks->getAction()->getListeners()->where('hook', 'my_great_action')->count(), 0);
        // check that the other action wasn't removed
        $this->assertEquals($this->hooks->getAction()->getListeners()->where('hook', 'my_great_action_2')->count(), 1);
    }
}