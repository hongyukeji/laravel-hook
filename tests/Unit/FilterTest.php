<?php

namespace HongyukejiTests\Unit;

use HongyukejiTests\DummyClass;
use PHPUnit\Framework\TestCase;
use TorMorten\Hongyukeji\Hooks;

class FilterTest extends TestCase
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
        $this->hooks->addFilter('my_awesome_filter', function ($value) {
            return $value . ' Filtered';
        });
        $this->assertEquals($this->hooks->filter('my_awesome_filter', 'Value Was'), 'Value Was Filtered');
    }

    /**
     * @test
     */
    public function it_can_hook_an_array()
    {
        $class = new class('DummyClass')
        {
            public function filter($value)
            {
                return $value . ' Filtered';
            }
        };
        $this->hooks->addFilter('my_amazing_filter', [$class, 'filter']);

        $this->assertEquals($this->hooks->filter('my_amazing_filter', 'Value Was'), 'Value Was Filtered');
    }

    /**
     * @test
     */
    public function a_hook_fires_even_if_there_are_two_listeners_with_the_same_priority()
    {
        $this->hooks->addFilter('my_great_filter', function ($value) {
            return $value . ' Once';
        }, 20);

        $this->hooks->addFilter('my_great_filter', function ($value) {
            return $value . ' And Twice';
        }, 20);

        $this->assertEquals($this->hooks->filter('my_great_filter', 'I Was Filtered'), 'I Was Filtered Once And Twice');
    }

    /**
     * @test
     */
    public function listeners_are_sorted_by_priority()
    {
        $this->hooks->addFilter('my_awesome_filter', function ($value) {
            return $value . ' Filtered';
        }, 20);

        $this->hooks->addFilter('my_awesome_filter', function ($value) {
            return $value . ' Filtered';
        }, 8);

        $this->hooks->addFilter('my_awesome_filter', function ($value) {
            return $value . ' Filtered';
        }, 12);

        $this->hooks->addFilter('my_awesome_filter', function ($value) {
            return $value . ' Filtered';
        }, 40);

        $this->assertEquals($this->hooks->getFilter()->getListeners()->values()[0]['priority'], 8);
        $this->assertEquals($this->hooks->getFilter()->getListeners()->values()[1]['priority'], 12);
        $this->assertEquals($this->hooks->getFilter()->getListeners()->values()[2]['priority'], 20);
        $this->assertEquals($this->hooks->getFilter()->getListeners()->values()[3]['priority'], 40);
    }

    /**
     * @test
     */
    public function a_single_filter_is_removed()
    {
        // check the collection has 1 item
        $this->hooks->addFilter('my_awesome_filter', 'my_awesome_function', 30, 1);
        $this->assertEquals($this->hooks->getFilter()->getListeners()->where('hook', 'my_awesome_filter')->count(), 1);

        // check removeFilter removes the filter
        $this->hooks->removeFilter('my_awesome_filter', 'my_awesome_function', 30);
        $this->assertEquals($this->hooks->getFilter()->getListeners()->where('hook', 'my_awesome_filter')->count(), 0);
    }

    /**
     * @test
     */
    public function all_filters_removed()
    {
        // check the collection has 3 items before checking they're removed
        $this->hooks->addFilter('my_awesome_filter', 'my_awesome_function', 30, 1);
        $this->hooks->addFilter('my_awesome_filter', 'my_other_awesome_function', 30, 1);
        $this->hooks->addFilter('my_awesome_filter_2', 'my_awesome_function_2', 30, 1);
        $this->assertEquals($this->hooks->getFilter()->getListeners()->count(), 3);

        // check removeFilter removes the filter
        $this->hooks->removeAllFilters();
        $this->assertEquals($this->hooks->getFilter()->getListeners()->count(), 0);
    }

    /**
     * @test
     */
    public function all_filters_removed_by_hook()
    {
        // check the collection has 1 item
        $this->hooks->addFilter('my_awesome_filter', 'my_awesome_function', 30, 1);
        $this->hooks->addFilter('my_awesome_filter', 'my_other_awesome_function', 30, 1);
        $this->hooks->addFilter('my_awesome_filter_2', 'my_awesome_function', 30, 1);
        $this->assertEquals($this->hooks->getFilter()->getListeners()->count(), 3);

        // check removeFilter removes the filter
        $this->hooks->removeAllFilters('my_awesome_filter');
        $this->assertEquals($this->hooks->getFilter()->getListeners()->where('hook', 'my_awesome_filter')->count(), 0);
        // check that the other filter wasn't removed
        $this->assertEquals($this->hooks->getFilter()->getListeners()->where('hook', 'my_awesome_filter_2')->count(), 1);
    }

    /** @test * */
    public function parameters_can_be_null()
    {
        $this->hooks->addFilter('my_awesome_filter', function ($one, $two) {
            return $one . ' Yay';
        }, 30, 2);

        $this->assertEquals($this->hooks->filter('my_awesome_filter', 'Woo', null), 'Woo Yay');
    }
}