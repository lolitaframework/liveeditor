<?php
namespace liveeditor\LolitaFramework\Configuration\Modules;

use \liveeditor\LolitaFramework\Configuration\Configuration;
use \liveeditor\LolitaFramework\Configuration\IModule;
use \liveeditor\LolitaFramework\Core\Arr;

class RemoveActions extends Actions
{

    /**
     * Remove actions
     *
     * @author Guriev Eugen <gurievcreative@gmail.com>
     * @return Actions instance.
     */
    protected function install()
    {
        foreach ($this->data as $data) {
            remove_action($data[0], $data[1], $data[2], $data[3]);
        }
        return $this;
    }
}
