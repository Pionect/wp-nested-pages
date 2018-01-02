<?php 
namespace NestedPages\Entities\NavMenu;

use NestedPages\Entities\NavMenu\NavMenuRepository;
use NestedPages\Entities\PluginIntegration\IntegrationFactory;

/**
* Base Nav Menu Sync class
*/
abstract class NavMenuSync 
{
	/**
	* Nav Menu Repository
	* @var object NavMenuRepository
	*/
	protected $nav_menu_repo;

	/**
	* The Menu ID
	* @var int
	*/
	protected $id;

	/**
	* Plugin Integrations
	* @var object
	*/
	protected $integrations;

	public function __construct()
	{
		$this->nav_menu_repo = new NavMenuRepository;
		$this->integrations = new IntegrationFactory;
		$this->setMenuID();
	}

	/**
	* Menu ID Setter
	*/
	protected function setMenuID()
	{
		$this->id = $this->nav_menu_repo->getMenuID();
	}

	/**
	 * Remove a Menu Item
	 * @since 1.3.4
	 *
	 * @param int $id - ID of nav menu item
	 */
	protected function removeItem( $id ) {

		$recurse = apply_filters('nestedpages_recursive_remove_menu_item', false, $id);
		if ( ! empty( $id ) && $recurse ) {

			$args = array(
				'post_type'  => 'nav_menu_item',
				'meta_query' => array(
					array(
						'key'   => '_menu_item_menu_item_parent',
						'value' => $id
					)
				),
				'fields'     => 'ids'
			);

			$children = get_posts( $args );

			if ( $children ) {
				foreach ( $children as $child ) {
					$this->removeItem( $child, true );
				}
			}
		}

		wp_delete_post( $id, true );
	}
}
