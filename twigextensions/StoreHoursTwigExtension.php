<?php
namespace Craft;

class StoreHoursTwigExtension extends \Twig_Extension
{

	public function getName()
	{
		return 'Store Hours';
	}

	public function getFilters()
	{
		return [
			'list_store_hours' => new \Twig_Filter_Method($this, 'twig_list_store_hours')
		];
	}

	public function initRuntime(\Twig_Environment $env)
	{
	}

	/**
	* Builds a list of readable opening hours rows
	* FIXME: This is horribly convoluted
	*
	* @param array $storeHours
	* @return array
	*/
	public function twig_list_store_hours($storeHours, $closedLabel = '&mdash;', $days = null)
	{
		if (empty($days)) {
			$days = [
				'Mo' => 1,
				'Tu' => 2,
				'We' => 3,
				'Th' => 4,
				'Fr' => 5,
				'Sa' => 6,
				'Su' => 0,
			];
		}

		$metaDays = [
			0 => 'Su',
			1 => 'Mo',
			2 => 'Tu',
			3 => 'We',
			4 => 'Th',
			5 => 'Fr',
			6 => 'Sa',
		];

		$prev = null;
		$dayLabels = [];
		$r = [];

		foreach ($days as $day => $index) {
			$h = $storeHours[$index];

			if (!empty($h['open']) && !empty($h['close'])) {
				$hours = sprintf(
					'%1$s &ndash; %2$s',
					$h['open']->localeTime(),
					$h['close']->localeTime()
				);
			}
			else {
				$hours = $closedLabel;
			}

			if (!empty($prev) && $hours != $prev) {
				if (count($dayLabels) < 3) {
					$key = implode(', ', $dayLabels);
				}
				else {
					$key = $dayLabels[0];
					if (!empty($dayLabels)) {
						$key .= ' &ndash; ' . $dayLabels[count($dayLabels) - 1];
					}
				}

				$meta = [];
				foreach ($dayLabels as $k => $v) {
					$meta[] = $metaDays[$days[$v]];
				}
				$meta = implode(',', $meta);
				$r[ $key ] = sprintf(
					'<span itemprop="openingHours" content="%1$s %2$s">%3$s</span>',
					$meta,
					str_replace(' &ndash; ', '-', $prev),
					$prev
				);

				$prev = null;
				$dayLabels = [];
				$dayLabels[] = $day;
			}
			else {
				$dayLabels[] = $day;
			}
			$prev = $hours;
		}

		if (count($dayLabels) < 3) {
			$key = implode(', ', $dayLabels);
		}
		else {
			$key = $dayLabels[0];
			if (!empty($dayLabels)) {
				$key .= ' &ndash; ' . $dayLabels[count($dayLabels) - 1];
			}
		}

		$meta = [];
		foreach ($dayLabels as $k => $v) {
			$meta[] = $metaDays[$days[$v]];
		}
		$meta = implode(',', $meta);
		$r[ $key ] = sprintf(
			'<span itemprop="openingHours" content="%1$s %2$s">%3$s</span>',
			$meta,
			str_replace(' &ndash; ', '-', $hours),
			$hours
		);

		return $r;
	}

}
