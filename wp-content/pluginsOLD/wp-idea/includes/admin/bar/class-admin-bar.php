<?php

namespace bpmj\wpidea\admin\bar;

use bpmj\wpidea\admin\bar\exceptions\Invalid_Admin_Bar_Configuration_Exception;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\admin\support\Support;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\translator\Interface_Translator;
use WP_Admin_Bar;

class Admin_Bar implements Interface_Initiable
{
    private const ITEM_ID_MAIN = 'bpmj-main';
    private const ITEM_ID_SUPPORT = 'lms-support';
    private const ITEM_ID_LICENSE_INFO = 'wpi-license-info';

    protected array $registered = [];

    protected WP_Admin_Bar $wp_admin_bar;

    private Interface_Actions $actions;
    private Interface_Translator $translator;

    public function __construct(
        Interface_Actions $actions,
        Interface_Translator $translator
    ) {
        $this->actions = $actions;
        $this->translator = $translator;
    }

    public function init(): void
    {
        $this->actions->add('admin_bar_menu', [$this, 'add_admin_bar_buttons'], 100);
    }

    public function register_item(string $id, string $title, Admin_Bar_Item_Position $position, ?string $href = null, array $attributes = []): void
    {
        $this->registered[] = [
            'position' => $position,
            'item' => new Admin_Bar_Item($id, $title, $href, null, false, $attributes)
        ];
    }

    protected function get_registered(): array
    {
        return $this->registered;
    }

    public function add_admin_bar_buttons($admin_bar): void
    {

        $this->set_wp_admin_bar($admin_bar);

        $this->create_and_render_item(
            self::ITEM_ID_MAIN,
            $this->translator->translate('admin_bar.bpmj_main') . ': '
        );

        $this->create_and_render_item(
            self::ITEM_ID_SUPPORT,
            $this->translator->translate('admin_bar.support'),
            admin_url('admin.php?page=' . Support::URL_SLUG)
        );

        $this->render_registered_buttons(Admin_Bar_Item_Position::from_string(Admin_Bar_Item_Position::INSIDE_WPI_INFO_BAR));

        $this->create_and_render_item(
            self::ITEM_ID_LICENSE_INFO,
            sprintf(__($this->translator->translate('admin_bar.license_info'), BPMJ_EDDCM_DOMAIN),'<strong>' . WPI()->packages->package . '</strong>')
        );

        $this->render_registered_buttons(Admin_Bar_Item_Position::from_string(Admin_Bar_Item_Position::BEFORE_USER_INFO));
    }

    protected function create_and_render_item(string $id, string $title, ?string $href = null, ?string $parent = null, bool $group = false, array $meta = []): void
    {
        $this->render_item(new Admin_Bar_Item($id, $title, $href, $parent, $group, $meta));
    }

    protected function render_registered_buttons(Admin_Bar_Item_Position $target_position): void
    {
        foreach ($this->get_registered() as $registered_item_data) {
            /** @var Admin_Bar_Item_Position $item_position */
            $item_position = $registered_item_data['position'] ?? null;
            /** @var Admin_Bar_Item $item */
            $item = $registered_item_data['item'] ?? null;

            if(!$item_position || !$item) {
                throw new Invalid_Admin_Bar_Configuration_Exception('Invalid item data!');
            }

            if(!$item_position->equals($target_position)) {
                continue;
            }

            if($target_position->equals(Admin_Bar_Item_Position::from_string(Admin_Bar_Item_Position::BEFORE_USER_INFO))) {
                $item->set_parent('top-secondary');
            }

            $this->render_item($item);
        }
    }

    protected function render_item(Admin_Bar_Item $item): void
    {
        if(!$this->is_wp_admin_bar_set()) return;

        $this->get_wp_admin_bar()->add_menu(array(
            'id' => $item->get_id(),
            'parent' => $item->get_parent(),
            'title' => $item->get_title(),
            'href' => $item->get_href(),
            'group' => $item->get_group(),
            'meta' => $item->get_meta()
        ));
    }

    protected function set_wp_admin_bar(WP_Admin_Bar $admin_bar): void
    {
        $this->wp_admin_bar = $admin_bar;
    }

    protected function is_wp_admin_bar_set(): bool
    {
        return !empty($this->get_wp_admin_bar());
    }

    protected function get_wp_admin_bar(): ?WP_Admin_Bar
    {
        return $this->wp_admin_bar;
    }
}