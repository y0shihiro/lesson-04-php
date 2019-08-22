<?php

namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * Questions Model
 */
class QuestionsTable extends Table
{
  /**
   * @inheritdoc
   */
  public function initialize(array $config)
  {
    parent::initialize($config);

    $this->setTable('questions'); //使用されるテーブル名
    $this->setDisplayField('id'); //list形式でデータ取得する際に使用されるカラム名
    $this->setPrimaryKey('id'); //プライマリキーとなるカラム名

    $this->addBehavior('Timestamp'); //created及びmodifiedカラムを自動設定する

    $this->hasMany('Answers', [
      'foreignKey' => 'question_id'
    ]);
  }
}