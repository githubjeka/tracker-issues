<?php

namespace yii\web {

    function move_uploaded_file()
    {
        return true;
    }
}

namespace tracker\controllers\services {

    function mkdir()
    {
        return true;
    }
}

namespace tracker\tests\unit {

    use humhub\modules\user\models\User;
    use humhub\modules\user\tests\codeception\fixtures\UserFixture;
    use tracker\controllers\services\DocumentCreator;
    use tracker\enum\ContentVisibilityEnum;
    use tracker\enum\IssuePriorityEnum;
    use tracker\enum\IssueStatusEnum;
    use tracker\models\Document;
    use tracker\tests\fixtures\DocumentFileFixture;

    /**
     * @author Evgeniy Tkachenko <et.coder@gmail.com>
     * @property \tracker\UnitTester tester
     */
    class DocumentTest extends ServiceTest
    {
        /**
         * @Override
         */
        protected function _before()
        {
            $this->tester->haveFixtures([
                'document' => DocumentFileFixture::class,
                'space' => \humhub\modules\space\tests\codeception\fixtures\SpaceFixture::class,
            ]);

            \Yii::$app->user->switchIdentity(User::findOne(['id' => 1]));
        }

        public function testWorkWithCreator()
        {
            $documentCreator = new DocumentCreator();
            $this->assertFalse($documentCreator->create());
            $this->assertTrue($documentCreator->getDocumentForm()->hasErrors());

            $requireAttributes = [
                'name' => 'TEST DOCUMENT',
                'registeredAt' => '2017-05-01',
                'number' => '130/t-13-e',
            ];

            $_FILES['file'] = [
                'name' => 'myFile.jpg',
                'type' => 'image/jpeg',
                'error' => UPLOAD_ERR_OK,
                'size' => 1,
                'tmp_name' => '...myFile.jpg',
            ];

            $documentCreator->load($requireAttributes, '');
            $document = $documentCreator->create();
            $this->assertInstanceOf(Document::class, $document);
            $this->assertEquals($requireAttributes['name'], $document->name);
            $this->assertEquals($_FILES['file']['name'], $document->file->filename);
            $this->assertEmpty($document->category);
            $this->assertEmpty($document->type);
            $this->assertEmpty($document->from);
            $this->assertEmpty($document->to);
            $this->assertEquals($requireAttributes['number'], $document->number);
            $this->assertEquals(
                date_create_from_format('Y-m-d', $requireAttributes['registeredAt'])
                    ->setTime(0, 0)
                ->format('U'),
                $document->registered_at
            );
            $this->assertEmpty($document->description);
            $this->assertEquals(1, $document->created_by);
            $this->assertEquals(1, $document->file->created_by);
            $this->assertNotEmpty($document->created_at);
            $this->assertNotEmpty($document->file->created_at);
            $this->assertCount(0, $document->receivers);
            $this->assertCount(0, $document->issues);


            $documentCreator = new DocumentCreator();
            $document = $documentCreator->addReceiversTo($document);
            $document->refresh();
            $this->assertCount(0, $document->receivers);
            $this->assertCount(0, $document->issues);

            $this->tester->haveFixtures(['user' => UserFixture::class,]);
            $users = $this->tester->grabFixture('user')->data;
            $documentCreator->load(['receivers' => [$users[1]['guid'], $users[2]['guid']],], '');
            $documentCreator->addReceiversTo($document);
            $document->refresh();
            $this->assertCount(2, $document->receivers);
            $this->assertCount(2, $document->issues);
            foreach ($document->receivers as $receiver) {
                $this->assertEquals(0, $receiver->view_mark);
                $this->assertNotEmpty($receiver->created_at);
                $this->assertEmpty($receiver->viewed_at);
            }
            foreach ($document->issues as $issue) {
                $this->assertCount(0, $issue->assignees);
                $this->assertEquals($document->name, $issue->title);
                $this->assertEquals($document->description, $issue->description);
                $this->assertEquals(IssueStatusEnum::TYPE_WORK, $issue->status);
                $this->assertEquals(IssuePriorityEnum::TYPE_URGENT, $issue->priority);
                $this->assertEquals(ContentVisibilityEnum::TYPE_PRIVATE, $issue->content->visibility);
            }

            //again add
            $documentCreator->addReceiversTo($document);
            $document->refresh();
            $this->assertCount(2, $document->receivers);
            $this->assertCount(2, $document->issues);
        }
    }
}
