<?php

namespace tracker\tests\unit;

use tracker\models\Document;
use tracker\models\DocumentFileEntity;
use tracker\tests\fixtures\DocumentFixture;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 * @property \tracker\UnitTester tester
 */
class DocumentFileEntityTest extends \Codeception\Test\Unit
{
    public function testMoveToNewCategory()
    {
        $this->tester->haveFixtures([
            'documents' => [
                'class' => DocumentFixture::class,
                'dataFile' => '@tracker/tests/fixtures/data/documents.php'
            ]
        ]);

        /** @var Document $defaultDocument */
        $defaultDocument = $this->tester->grabFixture('documents', 'default');

        $file = new DocumentFileEntity($defaultDocument);

        $this->assertTrue($file->moveToNewCategory());
    }
}
