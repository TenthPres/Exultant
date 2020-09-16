<?php

namespace tp\TenthTemplate;

class TenthMenu {

    private string $menuLocation;
    private array $params;

    /**
     * TenthMenu constructor.
     *
     * @param string $menuLocation
     * @param array  $params
     */
    public function __construct(string $menuLocation, array $params)
    {
        if (! has_nav_menu($menuLocation))
            return false;

        $this->menuLocation = $menuLocation;
        $this->params = $params;

        return $this;
    }

    public function __toString()
    {
        $params = array_merge($this->params, ['theme_location' => $this->menuLocation]);
        ob_start();
        wp_nav_menu($params);
        return ob_get_clean();
    }

    public function columns() {
        $menuItems = wp_get_nav_menu_items(get_term(get_nav_menu_locations()[$this->menuLocation], 'nav_menu')->name);

        $columnCount = 0;
        foreach($menuItems as $key => $itm) {
            if ($itm->menu_item_parent === '0' || $itm->menu_item_parent === 0) {
                $columnCount++;
            };
        }
        return $columnCount;
    }
}