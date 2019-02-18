<?php
/**
 * @link https://github.com/LAV45/yii2-activity-logger
 * @copyright Copyright (c) 2017 LAV45
 * @author Alexey Loban <lav451@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace lav45\activityLogger\modules\controllers;

use Yii;
use yii\web\Controller;
use lav45\activityLogger\modules\models\ActivityLogSearch;
use lav45\activityLogger\modules\models\ActivityLogViewModel;

/**
 * Class DefaultController
 * @package lav45\activityLogger\modules\controllers
 *
 * @property \lav45\activityLogger\modules\Module $module
 */
class DefaultController extends Controller
{
    public function actionIndex()
    {
        ActivityLogViewModel::setModule($this->module);

        $searchModel = new ActivityLogSearch();
        $searchModel->setEntityMap($this->module->entityMap);
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());

		if (empty($searchModel->date)) {
			$searchModel->date = date('d.m.Y', strtotime("-1 week"));
		}
		if (empty($searchModel->to_date)) {
			$searchModel->to_date = date('d.m.Y');
		}

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

	public function actionReport()
	{
		ActivityLogViewModel::setModule($this->module);

		$searchModel = new ActivityLogSearch();
		$searchModel->setEntityMap($this->module->entityMap);
		if (empty($searchModel->date)) {
			$searchModel->date = date('d.m.Y', strtotime("-1 week"));
		}
		if (empty($searchModel->to_date)) {
			$searchModel->to_date = date('d.m.Y');
		}
		$report_data = $searchModel->searchReport(Yii::$app->getRequest()->getQueryParams());

		return $this->render('report', [
			'searchModel' => $searchModel,
			'report_data' => $report_data,
		]);
	}
}
