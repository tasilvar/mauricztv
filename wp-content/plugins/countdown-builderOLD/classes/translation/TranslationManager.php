<?php
namespace ycd;
require_once (dirname(__FILE__).'/AbstractTranslationManager.php');

class TranslationManager extends AbstractTranslationManager {
	public function renderTranslations() {

		if (!$this->translations) {
			return '<p>'.esc_attr(__('There is not translations', YCD_TEXT_DOMAIN)).'</p>';
		}
		$data = '';
		foreach ($this->translations as $key => $translation) {
			$translation['id'] = $key;
			$data .= $this->translationRow($translation);
		}

		return $data;
	}
	public function render() {
		ob_start();
		?>
		<div class="row">
			<div class="col-md-12 ycd-container-wrapper">
				<?php echo $this->renderTranslations(); ?>
			</div>
			<div class="col-md-12">
				<?php echo $this->addTranslationButton(); ?>
			</div>
		</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
}