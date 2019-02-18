<?php
/**
 * @link https://github.com/LAV45/yii2-activity-logger
 * @copyright Copyright (c) 2017 LAV45
 * @author Alexey Loban <lav451@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace lav45\activityLogger\modules\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Class ActivityLogSearch
 * @package lav45\activityLogger\modules\models
 */
class ActivityLogSearch extends Model
{
    /**
     * @var string
     */
    public $entityName;
    /**
     * @var int|string
     */
    public $entityId;
    /**
     * @var int|string
     */
    public $userId;
    /**
     * @var string
     */
    public $env;
    /**
     * @var string
     */
    public $date;

	/**
	 * @var string
	 */
	public $to_date;
    /**
     * @var array
     */
    private $entityMap;

    /**
     * @inheritdoc
     */
	public function rules()
	{
		return [
			[['date', 'to_date'], 'required'],

			[['entityName'], 'in', 'range' => array_keys($this->getEntityMap())],

			[['entityId'], 'safe'],

			[['userId'], 'safe'],

			[['env'], 'safe'],

			[['date', 'to_date'], 'date', 'format' => 'dd.MM.yyyy'],

			['date', function() {
				if (strtotime($this->date) > strtotime($this->to_date)) {
					$this->addError('date', 'Дата начала не может быть больше даты окончания');
				}
			}],

			['to_date', function() {
				if (strtotime($this->to_date) < strtotime($this->date)) {
					$this->addError('to_date', 'Дата окончания не может быть меньше даты начала');
				}
			}],
		];
	}

    /**
     * For beautiful links in the browser bar when filtering and searching
     * @return string
     */
    public function formName()
    {
        return '';
    }

	/**
	 * Creates data provider instance with search query applied
	 * @param array $params
	 * @return ActiveDataProvider
	 */
	public function search($params)
	{
		$query = ActivityLogViewModel::find()
			->orderBy(['id' => SORT_DESC]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => false,
		]);

		if (empty($this->date)) {
			$this->date = date('d.m.Y', strtotime("-1 week"));
		}
		if (empty($this->to_date)) {
			$this->to_date = date('d.m.Y');
		}
		$this->load($params, '');
		if (!$this->validate()) {
			return $dataProvider;
		}

		if (!empty($this->date)) {
			$date = Yii::$app->getFormatter()
				->asTimestamp($this->date . ' 00:00:00 ' . Yii::$app->timeZone);

			$query
				->andFilterWhere(['and',
					['>=', 'created_at', $date],
					/*['<=', 'created_at', $date + 86400],*/
				]);
		}

		if (!empty($this->to_date)) {
			$date = Yii::$app->getFormatter()
				->asTimestamp($this->to_date . ' 00:00:00 ' . Yii::$app->timeZone);

			$query
				->andFilterWhere(['and',
					['<=', 'created_at', $date + 86400],
				]);
		}

		$query
			->andFilterWhere(['entity_name' => $this->entityName])
			->andFilterWhere(['entity_id' => $this->entityId])
			->andFilterWhere(['user_id' => $this->userId])
			->andFilterWhere(['env' => $this->env]);

		return $dataProvider;
	}


	/**
	 * @param array $params
	 * @return array
	 */
	public function searchReport($params)
	{
		$report_data = [];
		$query = (new Query())->from('activity_log')->select('entity_name')->groupBy('entity_name')->orderBy(['entity_name' => SORT_DESC]);

		$this->load($params, '');

		if (!empty($this->date)) {
			$date = Yii::$app->getFormatter()
				->asTimestamp($this->date . ' 00:00:00 ' . Yii::$app->timeZone);

			$query
				->andFilterWhere(['and',
					['>=', 'created_at', $date],
					/*['<=', 'created_at', $date + 86400],*/
				]);
		}
		if (!empty($this->to_date)) {
			$date = Yii::$app->getFormatter()
				->asTimestamp($this->to_date . ' 00:00:00 ' . Yii::$app->timeZone);

			$query
				->andFilterWhere(['and',
					['<=', 'created_at', $date + 86400],
				]);
		}
		$query->andFilterWhere(['user_id' => $this->userId]);
		$entities = $query->all();

		foreach ($entities as $entity) {
			$query = (new Query())->from('activity_log')->select('count(*) as count, action')->where(['entity_name' => $entity['entity_name']])->groupBy('action')->orderBy('action');
			if (!empty($this->date)) {
				$date = Yii::$app->getFormatter()
					->asTimestamp($this->date . ' 00:00:00 ' . Yii::$app->timeZone);

				$query
					->andFilterWhere(['and',
						['>=', 'created_at', $date],
						/*['<=', 'created_at', $date + 86400],*/
					]);
			}
			if (!empty($this->to_date)) {
				$date = Yii::$app->getFormatter()
					->asTimestamp($this->to_date . ' 00:00:00 ' . Yii::$app->timeZone);

				$query
					->andFilterWhere(['and',
						['<=', 'created_at', $date + 86400],
					]);
			}
			$query->andFilterWhere(['user_id' => $this->userId]);

			$entity_actions = $query->all();
			if (!empty($entity_actions)) {
				$actions_info = [];
				foreach ($entity_actions as $action_info) {
					$actions_info[] = ['action' => $action_info['action'], 'count' => $action_info['count']];
				}
				$report_data[$entity['entity_name']] = $actions_info;
			}
		}
		return $report_data;
	}

    /**
     * @return array
     */
    protected function getEntityMap()
    {
        return $this->entityMap;
    }

    /**
     * @param array $value
     */
    public function setEntityMap($value)
    {
        $this->entityMap = $value;
    }

    /**
     * @return array
     */
    public function getEntityNameList()
    {
        $data = array_keys($this->getEntityMap());
        return array_combine($data, $data);
    }

	/**
	 * @return array
	 */
	public function getUsersList() {
		$users_model = ActivityLog::find()->select('user_id, user_name')->groupBy(['user_id'])->asArray()->all();
		$users = [];
		foreach ($users_model as $user) {
			$users[$user['user_id']] = $user['user_name'];
		}
		return $users;
	}
}