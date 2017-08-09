<?php

namespace tracker\controllers\services;

use tracker\models\Issue;
use tracker\models\Tag;
use tracker\models\TagsIssues;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class TagDesignator extends IssueService
{
    public function __construct(Issue $issue, array $config = [])
    {
        parent::__construct($config);

        $this->issueModel = $issue;
        $this->requestForm->id = $issue->id;

        $tags = [];
        foreach ($this->issueModel->personalTags as $tag) {
            $tags[] = $tag->id;
        }
        $this->requestForm->tags = $tags;
    }

    public function save()
    {
        $oldTags = $this->issueModel->personalTags;

        if (is_array($this->requestForm->tags)) {
            foreach ($this->requestForm->tags as $tagId) {

                $key = array_search($tagId, $oldTags, true);

                if ($key === false) {
                    $tagModel = Tag::find()->byUser(\Yii::$app->user->getId())->andWhere(['id' => $tagId])->one();
                    $relationModel = new TagsIssues();
                    $relationModel->issue_id = $this->issueModel->id;
                    $relationModel->tag_id = $tagModel->id;
                    $relationModel->save(false);
                } else {
                    $oldTags[$key] = null;
                }
            }
        }

        foreach ($oldTags as $tagModel) {
            if ($tagModel !== null) {
                $relationModel = TagsIssues::find()->where([
                    'issue_id' => $this->issueModel->id,
                    'tag_id' => $tagModel->id,
                ])->one();
                $relationModel->delete();
            }
        }

        return true;
    }
}
