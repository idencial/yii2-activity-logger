<?php
/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel lav45\activityLogger\modules\models\ActivityLogSearch
 */

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$this->title = Yii::t('lav45/logger', 'Activity log');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss(<<<CSS
.logger-index .details {
    color: #7e7e7e;
    margin-bottom: 1.5em;
}
.logger-index .list-view {
    margin-top: 20px;
}
.logger-index .details-text {
    display: inline-block;
    vertical-align: top;
}
CSS
);

?>
<div class="logger-index">

    <h2><?= Html::encode($this->title) ?></h2>

	<?= $this->render('_search_report', ['model' => $searchModel]); ?>

	<?
	if (!empty($report_data)) {
		?>
        <table class="table table-striped table-bordered" style="width: 600px; margin-top: 20px;">
            <thead class="thead-light"><tr><th>Название рездела</th><th>Количество</th></tr></thead>
            <tbody>
			<?
			foreach ($report_data as $section_name=>$row) {
				?>
                <tr><td colspan="2"><strong><?=$section_name?></strong></td></tr>
				<?
				foreach ($row as $action) {
					?>
                    <tr><td><?=$action['action']?></td><td><?=$action['count']?></td></tr>
					<?
				}
			}
			?>
            </tbody>
        </table>
		<?
	}
	?>

</div>
