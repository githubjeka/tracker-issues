<?php

namespace tracker\widgets;

use tracker\Module;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueWallEntry extends \humhub\modules\content\widgets\WallEntry
{
    public $wallEntryLayout = 'wallEntry';

    public function run()
    {
        return $this->renderFile(__DIR__ . '/../views/issue/view.php', ['issue' => $this->contentObject,]);
    }

    public function getEditUrl()
    {
        $this->editRoute = '/' . Module::getIdentifier() . '/issue/edit';
        return parent::getEditUrl();
    }
}
