<?php

namespace Aventura\Edd\AddToCartPopup\Core;

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @version 1.1.0
 */
class HookLoader {

	const TYPE_ACTION = 'action';
	const TYPE_FILTER = 'filter';

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->actions = array();
		$this->filters = array();
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @var      string               $hook             The name of the WordPress action that is being registered.
	 * @var      object               $component        A reference to the instance of the object on which the action is defined.
	 * @var      string               $callback         The name of the function definition on the $component.
	 * @var      int      Optional    $priority         The priority at which the function should be fired.
	 * @var      int      Optional    $accepted_args    The number of arguments that should be passed to the $callback.
	 */
	public function addAction( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		return $this->add( self::TYPE_ACTION, false, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Queues a new action to the collection to be registered later with WordPress.
	 *
	 * @since    1.1.0
	 * @var      string               $hook             The name of the WordPress action that is being registered.
	 * @var      object               $component        A reference to the instance of the object on which the action is defined.
	 * @var      string               $callback         The name of the function definition on the $component.
	 * @var      int      Optional    $priority         The priority at which the function should be fired.
	 * @var      int      Optional    $accepted_args    The number of arguments that should be passed to the $callback.
	 */
	public function queueAction( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		return $this->add( self::TYPE_ACTION, true, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @var      string               $hook             The name of the WordPress filter that is being registered.
	 * @var      object               $component        A reference to the instance of the object on which the filter is defined.
	 * @var      string               $callback         The name of the function definition on the $component.
	 * @var      int      Optional    $priority         The priority at which the function should be fired.
	 * @var      int      Optional    $accepted_args    The number of arguments that should be passed to the $callback.
	 */
	public function addFilter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		return $this->add( self::TYPE_FILTER, false, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Queues a new filter to the collection to be registered later with WordPress.
	 *
	 * @since    1.1.0
	 * @var      string               $hook             The name of the WordPress filter that is being registered.
	 * @var      object               $component        A reference to the instance of the object on which the filter is defined.
	 * @var      string               $callback         The name of the function definition on the $component.
	 * @var      int      Optional    $priority         The priority at which the function should be fired.
	 * @var      int      Optional    $accepted_args    The number of arguments that should be passed to the $callback.
	 */
	public function queueFilter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		return $this->add( self::TYPE_FILTER, true, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string               $type             The hook type: 'filter' or 'action'
	 * @var      boolean              $queue            If true, the hook is queued to be registered later. If false, it is hooked immediately.
	 * @var      string               $hook             The name of the WordPress filter that is being registered.
	 * @var      object               $component        A reference to the instance of the object on which the filter is defined.
	 * @var      string               $callback         The name of the function definition on the $component.
	 * @var      int      Optional    $priority         The priority at which the function should be fired.
	 * @var      int      Optional    $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   type                                   The collection of actions and filters registered with WordPress.
	 */
	protected function add( $type, $queue, $hook, $component, $callback, $priority, $accepted_args ) {
		if ($queue) {
			// Add to appropriate array property
			$property = sprintf('%ss', $type);
			$this->{$property}[] = array(
				'hook'          => $hook,
				'component'     => $component,
				'callback'      => $callback,
				'priority'      => $priority,
				'accepted_args' => $accepted_args
			);
		} else {
			// Call function for hooking
			$fn = sprintf('add_%s', $type);
			$fullCallback = (($callback instanceof Closure) || is_null($component))
					? $callback
					: array($component, $callback);
			$args = array($hook, $fullCallback, $priority, $accepted_args);
			call_user_func_array($fn, $args);
		}
		// Return instance for chaining
		return $this;
	}

	/**
	 * Register the queued filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function registerQueue() {
		// Register queued filters
		foreach ( $this->filters as $hook ) {
			$this->add( self::TYPE_FILTER, false, $hook['hook'], $hook['component'], $hook['callback'], $hook['priority'], $hook['accepted_args'] );
		}
		// Register queued actions
		foreach ( $this->actions as $hook ) {
			$this->add( self::TYPE_ACTION, false, $hook['hook'], $hook['component'], $hook['callback'], $hook['priority'], $hook['accepted_args'] );
		}
		// Reset queues
		$this->filters = array();
		$this->actions = array();
	}

}