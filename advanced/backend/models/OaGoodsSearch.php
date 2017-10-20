<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\OaGoods;

/**
 * OaGoodsSearch represents the model behind the search form about `backend\models\OaGoods`.
 */
class OaGoodsSearch extends OaGoods
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
           // [['id'], 'integer'],
            [['img','cate', 'devNum', 'origin1', 'developer', 'introducer',
                'devStatus', 'checkStatus','subCate','vendor1','vendor2','vendor3',
                'origin2','origin3',
            ], 'string'],
            [['hopeRate','salePrice', 'hopeWeight','hopeMonthProfit','hopeSale','nid'], 'number'],
            [['createDate', 'updateDate'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params,$devstatus,$checkStatus)
    {
//        $query = OaGoods::find()->where(['devStatus'=>$devstatus]);
        $query = OaGoods::find()->orderBy(['nid' => SORT_DESC])->where(['<>','introducer',''])->andWhere(['<>','checkStatus','已作废']);

        if(!empty($checkStatus)){
            $query = OaGoods::find()->orderBy(['nid' => SORT_DESC])->where(['checkStatus'=>$checkStatus]);
        }

        if($devstatus=='devedUnchecked'){
            $query = OaGoods::find()->orderBy(['nid' => SORT_DESC])->where(['checkStatus'=>'待审批']);
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'nid' => $this->nid,
//            'hopeRate' => $this->hopeRate,
            'createDate' => $this->createDate,
            'updateDate' => $this->updateDate,
            'cate' => $this->cate,
            'subCate' => $this->subCate,
            'developer' => $this->developer,
            'introducer' => $this->introducer,
            'checkStatus' => $this->checkStatus,
        ]);

        $query->andFilterWhere(['like', 'cate', $this->cate])
            ->andFilterWhere(['like', 'subCate', $this->subCate])
            ->andFilterWhere(['like', 'devNum', $this->devNum])
            ->andFilterWhere(['like', 'origin1', $this->origin1])
            ->andFilterWhere(['like', 'developer', $this->developer])
            ->andFilterWhere(['like', 'introducer', $this->introducer])
            ->andFilterWhere(['like', 'devStatus', $this->devStatus])
            ->andFilterWhere(['like', 'checkStatus', $this->checkStatus]);

        return $dataProvider;
    }
}
