<?php
namespace craft\plugins\storehours\fields;

use Craft;
use craft\app\base\Element;
use craft\app\base\ElementInterface;
use craft\app\base\Field;
use craft\app\helpers\DateTimeHelper;
use craft\app\helpers\JsonHelper;
use yii\db\Schema;

/**
 * Store Hours field
 */
class StoreHours extends Field
{
	// Static
	// =========================================================================

	/**
	 * @inheritdoc
	 */
	public static function displayName()
	{
		return 'Store Hours';
	}

	// Public Methods
	// =========================================================================

	/**
	 * Returns the content column type.
	 *
	 * @return string
	 */
	public function getContentColumnType()
	{
		return Schema::TYPE_TEXT;
	}

	/**
	 * @param mixed                         $value
	 * @param Element|ElementInterface|null $element
	 *
	 * @return string
	 */
	public function getInputHtml($value, $element)
	{
		return Craft::$app->getView()->renderTemplate('storehours/input', array(
			'id'    => Craft::$app->getView()->formatInputId($this->handle),
			'name'  => $this->handle,
			'value' => $value,
		));
	}

	/**
	 * Prepares the field’s value for use.
	 *
	 * This method is called when the field’s value is first accessed from the element. For example, the first time
	 * `entry.myFieldHandle` is called from a template, or right before [[getInputHtml()]] is called. Whatever
	 * this method returns is what `entry.myFieldHandle` will likewise return, and what [[getInputHtml()]]’s $value
	 * argument will be set to.
	 *
	 * @param mixed                         $value   The raw field value
	 * @param ElementInterface|Element|null $element The element the field is associated with, if there is one
	 *
	 * @return mixed The prepared field value
	 */
	public function prepareValue($value, $element)
	{
		if (is_string($value))
		{
			$value = JsonHelper::decode($value);
		}

		$this->_convertTimes($value);

		return $value;
	}

	// Protected Methods
	// =========================================================================

	/**
	 * Prepares this field’s value on an element before it is saved.
	 *
	 * @param mixed                    $value   The field’s raw POST value
	 * @param ElementInterface|Element $element The element that is about to be saved
	 * @return mixed The field’s prepared value
	 */
	protected function prepareValueBeforeSave($value, $element)
	{
		$this->_convertTimes($value, Craft::$app->getTimeZone());

		return $value;
	}

	// Private Methods
	// =========================================================================

	/**
	 * Loops through the data and converts the times to DateTime objects.
	 *
	 * @param array &$value
	 * @param string $timezone
	 */
	private function _convertTimes(&$value, $timezone = null)
	{
		if (is_array($value))
		{
			foreach ($value as &$day)
			{
				if ((is_string($day['open']) && $day['open']) || (is_array($day['open']) && $day['open']['time']))
				{
					$day['open'] = DateTimeHelper::toDateTime($day['open'], $timezone);
				}
				else
				{
					$day['open'] = '';
				}

				if ((is_string($day['close']) && $day['close']) || (is_array($day['close']) && $day['close']['time']))
				{
					$day['close'] = DateTimeHelper::toDateTime($day['close'], $timezone);
				}
				else
				{
					$day['close'] = '';
				}
			}
		}
	}
}
