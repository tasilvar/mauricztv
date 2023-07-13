<?php

namespace bpmj\wpidea\modules\logs;

use bpmj\wpidea\modules\logs\infrastructure\events\Event_Handlers_Initiator;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module;
use Psr\Container\ContainerInterface;

class Logs_Module implements Interface_Module
{
	private ContainerInterface $container;

	public function __construct(
		ContainerInterface $container
	) {
		$this->container = $container;
	}

	public function init(): void
	{
		$this->container->get( Event_Handlers_Initiator::class );
	}

	public function get_routes(): array
	{
		return [];
	}

	public function get_translations(): array
	{
		return [
			'pl_PL' => [
				'logs.log_message.course_settings_edited'            => 'Wartość pola "%s" w ustawieniach kursu o ID %s została zmieniona. Wprowadzone przez: "%s". ([Dane do debugowania] value 1: "%s", value 2: "%s")',
				'logs.log_message.course_settings_edited.toggle.on'  => 'Pole typu toggle "%s" w ustawieniach kursu o ID %s zostało włączone. Wprowadzone przez: "%s". ([Dane do debugowania] value 1: "%s", value 2: "%s")',
				'logs.log_message.course_settings_edited.toggle.off' => 'Pole typu toggle "%s" w ustawieniach kursu o ID %s zostało wyłączone. Wprowadzone przez: "%s". ([Dane do debugowania] value 1: "%s", value 2: "%s")',
                'logs.log_message.course_table_edited.deleted' => 'Kurs "%s" (ID: %s) został usunięty przez: "%s".',
                'logs.log_message.course_table_edited.toggle.on' => 'Sprzedaż kursu "%s" (ID: %s) została wyłączona przez: "%s".',
                'logs.log_message.course_table_edited.toggle.off' => 'Sprzedaż kursu "%s" (ID: %s) została włączona przez: "%s".',

				'logs.log_message.service_settings_edited'            => 'Wartość pola "%s" w ustawieniach usługi o ID %s została zmieniona. Wprowadzone przez: "%s". ([Dane do debugowania] value 1: "%s", value 2: "%s")',
				'logs.log_message.service_settings_edited.toggle.on'  => 'Pole typu toggle "%s" w ustawieniach usługi o ID %s zostało włączone. Wprowadzone przez: "%s". ([Dane do debugowania] value 1: "%s", value 2: "%s")',
				'logs.log_message.service_settings_edited.toggle.off' => 'Pole typu toggle "%s" w ustawieniach usługi o ID %s zostało wyłączone. Wprowadzone przez: "%s". ([Dane do debugowania] value 1: "%s", value 2: "%s")',
                'logs.log_message.service_table_edited.deleted' => 'Usługa "%s" (ID: %s) została usunięta przez: "%s".',
                'logs.log_message.service_table_edited.toggle.on' => 'Sprzedaż usługi "%s" (ID: %s) została wyłączona przez: "%s".',
                'logs.log_message.service_table_edited.toggle.off' => 'Sprzedaż usługi "%s" (ID: %s) została włączona przez: "%s".',

				'logs.log_message.digital_product_settings_edited'            => 'Wartość pola "%s" w ustawieniach produktu cyfrowego o ID %s została zmieniona. Wprowadzone przez: "%s". ([Dane do debugowania] value 1: "%s", value 2: "%s")',
				'logs.log_message.digital_product_settings_edited.toggle.on'  => 'Pole typu toggle "%s" w ustawieniach produktu cyfrowego o ID %s zostało włączone. Wprowadzone przez: "%s". ([Dane do debugowania] value 1: "%s", value 2: "%s")',
				'logs.log_message.digital_product_settings_edited.toggle.off' => 'Pole typu toggle "%s" w ustawieniach produktu cyfrowego o ID %s zostało wyłączone. Wprowadzone przez: "%s". ([Dane do debugowania] value 1: "%s", value 2: "%s")',
                'logs.log_message.digital_product_table_edited.deleted' => 'Produkt cyfrowy "%s" (ID: %s) został usunięty przez: "%s".',
                'logs.log_message.digital_product_table_edited.toggle.on' => 'Sprzedaż produktu cyfrowego "%s" (ID: %s) została wyłączona przez: "%s".',
                'logs.log_message.digital_product_table_edited.toggle.off' => 'Sprzedaż produktu cyfrowego "%s" (ID: %s) została włączona przez: "%s".',

				'logs.log_message.physical_product_settings_edited'            => 'Wartość pola "%s" w ustawieniach produktu fizycznego o ID %s została zmieniona. Wprowadzone przez: "%s". ([Dane do debugowania] value 1: "%s", value 2: "%s")',
				'logs.log_message.physical_product_settings_edited.toggle.on'  => 'Pole typu toggle "%s" w ustawieniach produktu fizycznego o ID %s zostało włączone. Wprowadzone przez: "%s". ([Dane do debugowania] value 1: "%s", value 2: "%s")',
				'logs.log_message.physical_product_settings_edited.toggle.off' => 'Pole typu toggle "%s" w ustawieniach produktu fizycznego o ID %s zostało wyłączone. Wprowadzone przez: "%s". ([Dane do debugowania] value 1: "%s", value 2: "%s")',
                'logs.log_message.physical_product_table_edited.deleted' => 'Produkt fizyczny "%s" (ID: %s) został usunięty przez: "%s".',
                'logs.log_message.physical_product_table_edited.toggle.on' => 'Sprzedaż produktu fizycznego "%s" (ID: %s) została wyłączona przez: "%s".',
                'logs.log_message.physical_product_table_edited.toggle.off' => 'Sprzedaż produktu fizycznego "%s" (ID: %s) została włączona przez: "%s".',

				'logs.log_message.bundle_settings_edited'            => 'Wartość pola "%s" w ustawieniach pakietu o ID %s została zmieniona. Wprowadzone przez: "%s". ([Dane do debugowania] value 1: "%s", value 2: "%s")',
				'logs.log_message.bundle_settings_edited.toggle.on'  => 'Pole typu toggle "%s" w ustawieniach pakietu o ID %s zostało włączone. Wprowadzone przez: "%s". ([Dane do debugowania] value 1: "%s", value 2: "%s")',
				'logs.log_message.bundle_settings_edited.toggle.off' => 'Pole typu toggle "%s" w ustawieniach pakietu o ID %s zostało wyłączone. Wprowadzone przez: "%s". ([Dane do debugowania] value 1: "%s", value 2: "%s")',
                'logs.log_message.bundle_table_edited.deleted' => 'Pakiet "%s" (ID: %s) został usunięty przez: "%s".',
                'logs.log_message.bundle_table_edited.toggle.on' => 'Sprzedaż pakietu "%s" (ID: %s) została wyłączona przez: "%s".',
                'logs.log_message.bundle_table_edited.toggle.off' => 'Sprzedaż pakietu "%s" (ID: %s) została włączona przez: "%s".',

				'logs.log_message.main_settings_edited'            => 'Wartość pola "%s" w ustawieniach głównych Publigo została zmieniona. Wprowadzone przez: "%s". ([Dane do debugowania] value 1: "%s", value 2: "%s")',
				'logs.log_message.main_settings_edited.toggle.on'  => 'Pole typu toggle "%s" w ustawieniach głównych Publigo zostało włączone. Wprowadzone przez: "%s". ([Dane do debugowania] value 1: "%s", value 2: "%s")',
				'logs.log_message.main_settings_edited.toggle.off' => 'Pole typu toggle "%s" w ustawieniach głównych Publigo zostało wyłączone. Wprowadzone przez: "%s". ([Dane do debugowania] value 1: "%s", value 2: "%s")',

				'logs.log_message.plugin_updated'     => 'Wtyczka "%s" została zaktualizowana z wersji %s do %s.',
				'logs.log_message.plugin_updated_by'  => 'Wykonane przez: %s',
				'logs.log_message.plugin_deleted'     => 'Wtyczka "%s" została usunięta przez %s.',
				'logs.log_message.plugin_activated'   => 'Wtyczka "%s" została włączona przez %s.',
				'logs.log_message.plugin_deactivated' => 'Wtyczka "%s" została wyłączona przez %s.',

				'logs.log_message.page_updated.product_offer_description.digital_product' => 'Pełny opis oferty produktu cyfrowego (ID produktu: %2$s) został zaktualizowany. Wprowadzone przez: "%3$s".',
                'logs.log_message.page_updated.product_offer_description.course' => 'Pełny opis oferty kursu (ID kursu: %2$s) został zaktualizowany. Wprowadzone przez: "%3$s".',
                'logs.log_message.page_updated.product_offer_description.service' => 'Pełny opis oferty usługi (ID usługi: %2$s) został zaktualizowany. Wprowadzone przez: "%3$s".',
                'logs.log_message.page_updated.product_offer_description.physical_product' => 'Pełny opis oferty produktu fizycznego (ID produktu: %2$s) został zaktualizowany. Wprowadzone przez: "%3$s".',
                'logs.log_message.page_updated.product_offer_description.bundle' => 'Pełny opis oferty pakietu (ID pakietu: %2$s) został zaktualizowany. Wprowadzone przez: "%3$s".',

                'logs.log_message.page_updated.course_panel_content.course' => 'Powitanie / opis w panelu kursanta (ID kursu: %2$s) zostało zaktualizowane. Wprowadzone przez: "%3$s".',

                'logs.log_message.page_updated.course_module' => 'Moduł (ID: %1$s) należący do kursu o ID: %2$s został zaktualizowany. Wprowadzone przez: "%3$s".',
                'logs.log_message.page_updated.course_lesson' => 'Lekcja (ID: %1$s) należąca do kursu o ID: %2$s została zaktualizowany. Wprowadzone przez: "%3$s".',
                'logs.log_message.page_updated.course_test' => 'Quiz (ID: %1$s) należący do kursu o ID: %2$s został zaktualizowany. Wprowadzone przez: "%3$s".',

                'logs.log_message.page_updated.user_account_page' => 'Strona "Moje konto" (ID: %1$s) została zaktualizowana. Wprowadzone przez: "%3$s".',
                'logs.log_message.page_updated.courses_page' => 'Strona z listą produktów (ID: %1$s) została zaktualizowana. Wprowadzone przez: "%3$s".',
			],
			'en_US' => [

				'logs.log_message.course_settings_edited'            => '"%s" field value in the course settings (ID: %s) has been changed. Entered by: "%s". ([Data for debugging] value 1: "%s", value 2: "%s")',
				'logs.log_message.course_settings_edited.toggle.on'  => 'Toggle field "%s" in course settings with (ID: %s) has been enabled. Entered by: "%s". ([Data for debugging] value 1: "%s", value 2: "%s")',
				'logs.log_message.course_settings_edited.toggle.off' => 'Toggle field "%s" in course settings with (ID: %s) has been disabled. Entered by: "%s". ([Data for debugging] value 1: "%s", value 2: "%s")',

				'logs.log_message.service_settings_edited'            => '"%s" field value in the service settings (ID: %s) has been changed. Entered by: "%s". ([Data for debugging] value 1: "%s", value 2: "%s")',
				'logs.log_message.service_settings_edited.toggle.on'  => 'Toggle field "%s" in the service settings (ID: %s) has been enabled. Entered by: "%s". ([Data for debugging] value 1: "%s", value 2: "%s")',
				'logs.log_message.service_settings_edited.toggle.off' => 'Toggle field "%s" in the service settings (ID: %s) has been disabled. Entered by: "%s". ([Data for debugging] value 1: "%s", value 2: "%s")',

				'logs.log_message.digital_product_settings_edited'            => '"%s" field value in the digital product settings (ID: %s) has been changed. Entered by: "%s". ([Data for debugging] value 1: "%s", value 2: "%s")',
				'logs.log_message.digital_product_settings_edited.toggle.on'  => 'Toggle field "%s" in the digital product settings (ID: %s) has been enabled. Entered by: "%s". ([Data for debugging] value 1: "%s", value 2: "%s")',
				'logs.log_message.digital_product_settings_edited.toggle.off' => 'Toggle field "%s" in the digital product settings (ID: %s) has been disabled. Entered by: "%s". ([Data for debugging] value 1: "%s", value 2: "%s")',

				'logs.log_message.physical_product_settings_edited'            => '"%s" field value in the physical product settings (ID: %s) has been changed. Entered by: "%s". ([Data for debugging] value 1: "%s", value 2: "%s")',
				'logs.log_message.physical_product_settings_edited.toggle.on'  => 'Toggle field "%s" in the physical product settings (ID: %s) has been enabled. Entered by: "%s". ([Data for debugging] value 1: "%s", value 2: "%s")',
				'logs.log_message.physical_product_settings_edited.toggle.off' => 'Toggle field "%s" in the physical product settings (ID: %s) has been disabled. Entered by: "%s". ([Data for debugging] value 1: "%s", value 2: "%s")',

				'logs.log_message.bundle_settings_edited'            => '"%s" field value in the bundle settings (ID: %s) has been changed. Entered by: "%s". ([Data for debugging] value 1: "%s", value 2: "%s")',
				'logs.log_message.bundle_settings_edited.toggle.on'  => 'Toggle field "%s" in the bundle settings (ID: %s) has been enabled. Entered by: "%s". ([Data for debugging] value 1: "%s", value 2: "%s")',
				'logs.log_message.bundle_settings_edited.toggle.off' => 'Toggle field "%s" in the bundle settings (ID: %s) has been disabled. Entered by: "%s". ([Data for debugging] value 1: "%s", value 2: "%s")',

				'logs.log_message.main_settings_edited'            => '"%s" field in Publigo main settings has been changed. Entered by: "%3$s". ([Data for debugging] value 1: "%4$s", value 2: "%5$s")',
				'logs.log_message.main_settings_edited.toggle.on'  => 'Toggle field "%s" in Publigo main settings has been enabled. Submitted by: "%3$s". ([Data for debugging] value 1: "%4$s", value 2: "%5$s")',
				'logs.log_message.main_settings_edited.toggle.off' => 'Toggle field "%s" in Publigo main settings has been disabled. Submitted by: "%3$s". ([Data for debugging] value 1: "%4$s", value 2: "%5$s")',

				'logs.log_message.plugin_updated'     => 'Plugin "%s" was updated from version %s to %s.',
				'logs.log_message.plugin_updated_by'  => 'Done by: %s',
				'logs.log_message.plugin_deleted'     => 'Plugin "%s" was deleted by %s.',
				'logs.log_message.plugin_activated'   => 'Plugin "%s" was activated by %s.',
				'logs.log_message.plugin_deactivated' => 'Plugin "%s" was deactivated by %s.',

                'logs.log_message.page_updated.product_offer_description.digital_product' => 'The full digital product offer description (product ID: %2$s) has been updated. Submitted by: "%3$s".',
                'logs.log_message.page_updated.product_offer_description.course' => 'The full course offer description (course ID: %2$s) has been updated. Submitted by: "%3$s".',
                'logs.log_message.page_updated.product_offer_description.service' => 'The full description of the service offer (service ID: %2$s) has been updated. Submitted by: "%3$s".',
                'logs.log_message.page_updated.product_offer_description.physical_product' => 'Full physical product offer description (product ID: %2$s) updated. Submitted by: "%3$s".',
                'logs.log_message.page_updated.product_offer_description.bundle' => 'Bundle offer full description (bundle ID: %2$s) updated. Submitted by: "%3$s".',

                'logs.log_message.page_updated.course_panel_content.course' => 'The welcome / description in the student panel (course ID: %2$s) has been updated. Submitted by: "%3$s".',

                'logs.log_message.page_updated.course_module' => 'The module (ID: %1$s) belonging to course ID: %2$s has been updated. Submitted by: "%3$s".',
                'logs.log_message.page_updated.course_lesson' => 'Lesson (ID: %1$s) belonging to course ID: %2$s has been updated. Submitted by: "%3$s".',
                'logs.log_message.page_updated.course_test' => 'Quiz (ID: %1$s) belonging to course ID: %2$s has been updated. Submitted by: "%3$s".',

                'logs.log_message.page_updated.user_account_page' => 'My account page (ID: %1$s) has been updated. Submitted by: "%3$s".',
                'logs.log_message.page_updated.courses_page' => 'The product listing page (ID: %1$s) has been updated. Submitted by: "%3$s".',
			]
		];
	}
}