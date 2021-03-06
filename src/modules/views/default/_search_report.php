<?php
/**
 * @var $this yii\web\View
 * @var $model lav45\activityLogger\modules\models\ActivityLogSearch
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use dosamigos\datepicker\DatePicker;

if (isset(Yii::$app->params['datePicker-language'])) {
	$language = Yii::$app->params['datePicker-language'];
} else {
	$language = substr(Yii::$app->language, 0, 2);
}

?>
    <div class="logger-search">

		<?php $form = ActiveForm::begin([
			'method' => 'get',
			'action' => ['report'],
			'layout' => 'inline',
		]); ?>

		<?= $form->field($model, 'userId')->dropDownList($model->getUsersList(), [
			'prompt' => 'Выберете пользователя...',
		]) ?>

		<?= $form->field($model, 'date')->widget(DatePicker::class, [
			'language' => $language,
			'clientOptions' => [
				'autoclose' => true,
				'todayHighlight' => true,
				'format' => 'dd.mm.yyyy',
				'endDate' => date('d.m.Y'),
				'clearBtn' => true,
			],
		]) ?>

		<?= $form->field($model, 'to_date')->widget(DatePicker::class, [
			'language' => $language,
			'clientOptions' => [
				'autoclose' => true,
				'todayHighlight' => true,
				'format' => 'dd.mm.yyyy',
				'endDate' => date('d.m.Y'),
				'clearBtn' => true,
			],
		]) ?>

		<?= Html::submitButton(Yii::t('lav45/logger', 'Show'), [
			'class' => 'btn btn-default',
		]) ?>

		<?= Html::a(Yii::t('lav45/logger', 'Reset'), ['index'], [
			'class' => 'btn btn-default',
		]) ?>

		<?php ActiveForm::end(); ?>

    </div>

<?php
/*
$this->registerJs(<<<JS
    $('#{$form->id}').on('change', function() {
        $(this).submit();
    })
JS
);
*/
?>