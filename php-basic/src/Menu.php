<?php

namespace Losys\Demo;

class Menu {
    protected function getEntries(): array
    {
        return [
            'index.php'         => 'Default Settings',
            'no_search.php'     => 'No Search',
            'no_bootstrap.php'  => 'No Bootstrap',
            'list.php'          => 'Plain Listing',
            'json_listing.php'  => 'JSON data'
        ];
    }

    public function render(): string
    {
        $result = [];
        foreach($this->getEntries() as $file => $title)
            $result[] = '<a href="' . $file . '">' . htmlentities($title) . '</a><br />';

        return implode('', $result);
    }
}