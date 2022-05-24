<?php declare(strict_types = 1);

namespace App\System\Components\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Rendering\DefaultFormRenderer;

final class FormFactory
{

	public function create(): Form
	{
		return new Form();
	}

	public static function makeBootstrap4(Form $form): void
	{
		/** @var DefaultFormRenderer $renderer */
		$renderer = $form->getRenderer();
		$renderer->wrappers['controls']['container'] = null;
		$renderer->wrappers['pair']['container'] = 'div class="form-group"';
		$renderer->wrappers['pair']['.error'] = 'has-danger';
		$renderer->wrappers['control']['container'] = '';
		$renderer->wrappers['label']['container'] = '';
		$renderer->wrappers['control']['description'] = 'span class=form-text';
		$renderer->wrappers['control']['errorcontainer'] = 'span class=form-control-feedback';
		$renderer->wrappers['control']['.error'] = 'is-invalid';

		$usedPrimary = false;

		foreach ($form->getControls() as $control) {
			$type = $control->getOption('type');

			if ($type === 'button') {
				$control->getControlPrototype()->addClass($usedPrimary === false ? 'btn btn-primary' : 'btn btn-secondary');
				$usedPrimary = true;

			} elseif (in_array($type, ['text', 'textarea', 'select'], true)) {
				$control->getControlPrototype()->addClass('form-control');

			} elseif ($type === 'file') {
				$control->getControlPrototype()->addClass('form-control-file');

			} elseif (in_array($type, ['checkbox', 'radio'], true)) {
				if ($control instanceof Nette\Forms\Controls\Checkbox) {
					$control->getLabelPrototype()->addClass('form-check-label');
				} else {
					$control->getItemLabelPrototype()->addClass('form-check-label');
				}

				$control->getControlPrototype()->addClass('form-check-input');
				$control->getContainerPrototype()->setName('div')->addClass('form-check');
			}
		}
	}

}
